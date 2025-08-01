<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Job;
use App\Models\Application;
use App\Models\Admin;
use App\Notifications\WhatsAppCompanyRegistrationNotification;
use App\Notifications\WhatsAppJobStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::where('status', 'aktif')
                           ->where('is_approved', true)
                           ->with('category')
                           ->get();

        return response()->json([
            'success' => true,
            'data' => $companies
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'website' => 'nullable|url',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:jurusans,id',
            'established_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'company_size' => 'nullable|string',
            'contact_person' => 'required|string|max:255',
            'contact_person_phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $company = Company::create([
            'company_name' => $request->company_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'website' => $request->website,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'established_year' => $request->established_year,
            'company_size' => $request->company_size,
            'contact_person' => $request->contact_person,
            'contact_person_phone' => $request->contact_person_phone,
            'password' => Hash::make($request->password),
            'status' => 'pending',
        ]);

        // Kirim notifikasi WhatsApp ke admin dan company secara otomatis
        try {
            $company->notify(new \App\Notifications\WhatsAppCompanyRegistrationNotification(
                $company->company_name,
                $company->contact_person
            ));
        } catch (\Exception $e) {
            \Log::error('Failed to send WhatsApp notification for company registration', [
                'company_id' => $company->id,
                'error' => $e->getMessage()
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil. Menunggu persetujuan admin.',
            'data' => $company
        ], 201);
    }

    public function dashboard()
    {
        $company = Auth::user();
        
        $stats = [
            'total_jobs' => $company->jobs()->count(),
            'active_jobs' => $company->jobs()->where('status', 'active')->count(),
            'total_applications' => $company->applications()->count(),
            'new_applications' => $company->applications()->where('status', 'submitted')->count(),
            'accepted_applications' => $company->applications()->where('status', 'accepted')->count(),
        ];

        $recentApplications = Application::with(['alumni', 'job'])
                                        ->whereHas('job', function ($query) use ($company) {
                                            $query->where('company_id', $company->id);
                                        })
                                        ->latest('applied_at')
                                        ->limit(10)
                                        ->get();

        $jobPerformance = Job::with('applications')
                            ->where('company_id', $company->id)
                            ->withCount([
                                'applications',
                                'applications as pending_applications' => function ($query) {
                                    $query->where('status', 'submitted');
                                },
                                'applications as accepted_applications' => function ($query) {
                                    $query->where('status', 'accepted');
                                }
                            ])
                            ->latest()
                            ->limit(5)
                            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats,
                'recent_applications' => $recentApplications,
                'job_performance' => $jobPerformance,
            ]
        ]);
    }

    public function jobs()
    {
        $company = Auth::user();
        
        $jobs = Job::forCompany($company->id)
                   ->withCount('applications')
                   ->latest()
                   ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $jobs
        ]);
    }

    public function createJob(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'job_type' => 'required|in:' . implode(',', array_keys(Job::JOB_TYPES)),
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'deadline' => 'nullable|date|after:today',
            'status' => 'in:draft,active',
            'is_published' => 'boolean',
        ]);

        $company = Auth::user();

        $job = Job::create([
            'company_id' => $company->id,
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location,
            'job_type' => $request->job_type,
            'salary_min' => $request->salary_min,
            'salary_max' => $request->salary_max,
            'requirements' => $request->requirements,
            'benefits' => $request->benefits,
            'deadline' => $request->deadline,
            'status' => $request->status ?? 'draft',
            'is_published' => $request->is_published ?? false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lowongan berhasil dibuat',
            'data' => $job
        ], 201);
    }

    public function updateJob(Request $request, $id)
    {
        $company = Auth::user();
        $job = Job::where('company_id', $company->id)->findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'job_type' => 'required|in:' . implode(',', array_keys(Job::JOB_TYPES)),
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'deadline' => 'nullable|date|after:today',
            'status' => 'in:draft,active,closed',
            'is_published' => 'boolean',
        ]);

        $job->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Lowongan berhasil diperbarui',
            'data' => $job
        ]);
    }



    public function applications(Request $request)
    {
        $company = Auth::user();
        
        $query = Application::with(['alumni', 'job'])
                           ->whereHas('job', function ($q) use ($company) {
                               $q->where('company_id', $company->id);
                           });

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('job_id')) {
            $query->where('job_posting_id', $request->job_id);
        }

        $applications = $query->latest('applied_at')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $applications
        ]);
    }

    public function updateApplicationStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:reviewed,interview,accepted,rejected',
            'notes' => 'nullable|string',
        ]);

        $company = Auth::user();
        $application = Application::whereHas('job', function ($query) use ($company) {
                                     $query->where('company_id', $company->id);
                                   })
                                   ->findOrFail($id);

        $application->update([
            'status' => $request->status,
            'notes' => $request->notes,
            'reviewed_at' => now(),
        ]);

        // Send WhatsApp notification to alumni about status update
        try {
            $alumni = $application->alumni;
            if ($alumni) {
                $alumni->notify(new WhatsAppJobStatusNotification(
                    $application->job->title,
                    $application->job->company->company_name,
                    $request->status
                ));
            }
        } catch (\Exception $e) {
            // Log error but don't fail the status update
            \Log::error('Failed to send WhatsApp notification for status update', [
                'application_id' => $application->id,
                'status' => $request->status,
                'error' => $e->getMessage()
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status lamaran berhasil diperbarui',
            'data' => $application
        ]);
    }

    public function getApplicationDetail($id)
    {
        try {
            $application = Application::with(['alumni', 'job.company'])
                                     ->findOrFail($id);

            // Check if the current company can access this application
            if (auth()->check() && auth()->user()->hasRole('company')) {
                $company = auth()->user();
                $canAccess = $application->job->company_id === $company->id;
                if (!$canAccess) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized'
                    ], 403);
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $application->id,
                    'status' => $application->status,
                    'applied_at' => $application->created_at,
                    'cover_letter' => $application->cover_letter,
                    'cv_path' => $application->cv_path,
                    'notes' => $application->notes,
                    'reviewed_at' => $application->reviewed_at,
                    'alumni' => [
                        'id' => $application->alumni->id,
                        'name' => $application->alumni->nama,
                        'email' => $application->alumni->email,
                        'phone' => $application->alumni->phone,
                        'jurusan' => $application->alumni->jurusan->nama ?? '',
                        'tahun_lulus' => $application->alumni->tahun_lulus,
                    ],
                    'job' => [
                        'id' => $application->job->id,
                        'title' => $application->job->title,
                        'company_name' => $application->job->company->company_name,
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Application not found'
            ], 404);
        }
    }
}
