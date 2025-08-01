<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Job;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminCompanyController extends Controller
{
    public function index(Request $request)
    {
        $query = Company::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by industry
        if ($request->filled('industry')) {
            $query->where('industry', $request->industry);
        }

        // Sorting
        switch ($request->get('sort', 'newest')) {
            case 'oldest':
                $query->oldest();
                break;
            case 'name_asc':
                $query->orderBy('company_name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('company_name', 'desc');
                break;
            default:
                $query->latest();
        }

        // Add jobs count
        $query->withCount(['jobs']);

        $companies = $query->paginate(15)->withQueryString();

        return view('admin.companies.index', compact('companies'));
    }

    public function show(Company $company)
    {
        $company->load(['jobs' => function($query) {
            $query->latest()->take(5);
        }]);

        $company->loadCount(['jobs', 'applications' => function($query) {
            $query->whereHas('job', function($q) {
                $q->where('company_id', request()->route('company')->id);
            });
        }]);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $company
            ]);
        }

        return view('admin.companies.show', compact('company'));
    }

    public function create()
    {
        return view('admin.companies.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'company_name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url',
            'address' => 'nullable|string',
            'industry' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'required|in:active,pending,inactive',
        ]);

        $company = Company::create($validatedData);

        return redirect()
            ->route('admin.companies.index')
            ->with('success', 'Perusahaan berhasil ditambahkan.');
    }

    public function edit(Company $company)
    {
        return view('admin.companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $validatedData = $request->validate([
            'company_name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email,' . $company->id,
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url',
            'address' => 'nullable|string',
            'industry' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'required|in:active,pending,inactive',
        ]);

        $company->update($validatedData);

        return redirect()
            ->route('admin.companies.index')
            ->with('success', 'Data perusahaan berhasil diperbarui.');
    }

    public function destroy(Company $company)
    {
        try {
            // Check if company has active jobs
            if ($company->jobs()->where('status', 'active')->exists()) {
                return back()->with('error', 'Tidak dapat menghapus perusahaan yang memiliki lowongan aktif.');
            }

            DB::transaction(function() use ($company) {
                // Delete related data
                $company->jobs()->delete();
                $company->delete();
            });

            return redirect()
                ->route('admin.companies.index')
                ->with('success', 'Perusahaan berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menghapus perusahaan.');
        }
    }

    public function approve(Company $company)
    {
        $company->update(['status' => 'active']);

        return response()->json([
            'success' => true,
            'message' => 'Perusahaan berhasil disetujui.'
        ]);
    }

    public function reject(Company $company)
    {
        $company->update(['status' => 'inactive']);

        return response()->json([
            'success' => true,
            'message' => 'Perusahaan ditolak.'
        ]);
    }

    public function export(Request $request)
    {
        $query = Company::query();
        
        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('industry')) {
            $query->where('industry', $request->industry);
        }

        $companies = $query->withCount('jobs')->get();

        return response()->json([
            'success' => true,
            'data' => $companies,
            'message' => 'Data exported successfully'
        ]);
    }
}
