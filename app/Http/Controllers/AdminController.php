<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseBuilder;
use App\Http\Requests\CreateAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Models\Admin;
use App\Models\Alumni;
use App\Models\Company;
use App\Models\Job;
use App\Models\Application;
use App\Models\News;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $data = Admin::all();

        return ResponseBuilder::success()
            ->data($data)
            ->build();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateAdminRequest $request): JsonResponse
    {
        $admin = Admin::create($request->validated());
        return ResponseBuilder::success()
            ->data($admin)
            ->message('sukses membuat data admin baru')
            ->build();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $admin = Admin::query()->find($id);
        if (!$admin) {
            return ResponseBuilder::fail()
                ->message('data admin tidak ditemukan')
                ->httpCode(Response::HTTP_NOT_FOUND)
                ->build();
        }
        return ResponseBuilder::success()->data($admin)->build();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAdminRequest $request, string $id): JsonResponse
    {
        $admin = Admin::query()->find($id);
        if (!$admin) {
            return ResponseBuilder::fail()
                ->message('data admin tidak ditemukan')
                ->build();
        }

        $admin->update($request->validated());

        return ResponseBuilder::success()
            ->message('sukses memperbarui data admin')
            ->build();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $admin = Admin::query()->find($id);

        if (!$admin) {
            return ResponseBuilder::fail()
                ->message('data admin tidak ditemukan')
                ->build();
        }

        $admin->delete();

        return ResponseBuilder::success()
            ->message('data admin sukses dihapus')
            ->build();
    }

    /**
     * Get admin dashboard statistics
     */
    public function dashboard(): JsonResponse
    {
        $stats = [
            'total_alumni' => Alumni::count(),
            'total_companies' => Company::where('status', 'aktif')->count(),
            'pending_companies' => Company::where('status', 'pending')->count(),
            'total_jobs' => Job::where('status', 'active')->count(),
            'total_applications' => Application::count(),
            'new_applications' => Application::where('status', 'submitted')->count(),
            'published_news' => News::where('status', 'published')->count(),
        ];

        $recentApplications = Application::with(['alumni', 'job.company'])
                                        ->latest('applied_at')
                                        ->limit(10)
                                        ->get();

        $pendingCompanies = Company::where('status', 'pending')
                                  ->latest()
                                  ->limit(5)
                                  ->get();

        return ResponseBuilder::success()
            ->data([
                'stats' => $stats,
                'recent_applications' => $recentApplications,
                'pending_companies' => $pendingCompanies,
            ])
            ->build();
    }

    /**
     * Approve or reject company registration
     */
    public function approveCompany(Request $request, $id): JsonResponse
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'notes' => 'nullable|string',
        ]);

        $company = Company::find($id);
        if (!$company) {
            return ResponseBuilder::fail()
                ->message('Perusahaan tidak ditemukan')
                ->httpCode(Response::HTTP_NOT_FOUND)
                ->build();
        }

        if ($request->action === 'approve') {
            $company->update([
                'status' => 'aktif',
                'is_approved' => true,
            ]);
            $message = 'Perusahaan berhasil disetujui';
        } else {
            $company->update([
                'status' => 'rejected',
                'is_approved' => false,
            ]);
            $message = 'Perusahaan ditolak';
        }

        return ResponseBuilder::success()
            ->message($message)
            ->data($company)
            ->build();
    }

    /**
     * Get all applications for admin review
     */
    public function applications(Request $request): JsonResponse
    {
        $query = Application::with(['alumni', 'job.company']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('company_id')) {
            $query->whereHas('job', function ($q) use ($request) {
                $q->where('company_id', $request->company_id);
            });
        }

        $applications = $query->latest('applied_at')->paginate(10);

        return ResponseBuilder::success()
            ->data($applications)
            ->build();
    }

    /**
     * Get all companies for admin management
     */
    public function companies(Request $request): JsonResponse
    {
        $query = Company::with('category');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $companies = $query->latest()->paginate(10);

        return ResponseBuilder::success()
            ->data($companies)
            ->build();
    }

    /**
     * Get all jobs for admin management
     */
    public function jobs(Request $request): JsonResponse
    {
        $query = Job::with(['company']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        $jobs = $query->latest()->paginate(10);

        return ResponseBuilder::success()
            ->data($jobs)
            ->build();
    }
}
