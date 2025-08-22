<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyDashboardController extends Controller
{
    public function index()
    {
        $company = Auth::guard('company')->user();
        
        if (!$company) {
            return redirect()->route('login')->with('error', 'Please login as company to access dashboard.');
        }
        
        try {
            // Get statistics with error handling
            $stats = [
                'total_jobs' => 0,
                'active_jobs' => 0,
                'total_applications' => 0,
                'pending_applications' => 0,
            ];

            // Try to get job statistics
            try {
                $stats['total_jobs'] = Job::where('company_id', $company->getKey())->count();
                $stats['active_jobs'] = Job::where('company_id', $company->getKey())->where('status', 'active')->count();
            } catch (\Exception $e) {
                // Job table might not exist or have issues
            }

            // Try to get application statistics
            try {
                $stats['total_applications'] = Application::whereHas('job', function($query) use ($company) {
                    $query->where('company_id', $company->getKey());
                })->count();
                
                $stats['pending_applications'] = Application::whereHas('job', function($query) use ($company) {
                    $query->where('company_id', $company->getKey());
                })->where('status', 'pending')->count();
            } catch (\Exception $e) {
                // Application table might not exist or have issues
            }

            // Get recent jobs with error handling
            $recentJobs = collect();
            try {
                $recentJobs = Job::where('company_id', $company->getKey())
                    ->withCount('applications')
                    ->latest()
                    ->take(5)
                    ->get();
            } catch (\Exception $e) {
                // Job table might not exist
            }

            // Get recent applications with error handling
            $recentApplications = collect();
            try {
                $recentApplications = Application::with(['alumni', 'job'])
                    ->whereHas('job', function($query) use ($company) {
                        $query->where('company_id', $company->getKey());
                    })
                    ->latest()
                    ->take(5)
                    ->get();
            } catch (\Exception $e) {
                // Application table might not exist
            }

            return view('company.dashboard', compact('stats', 'recentJobs', 'recentApplications'));
        } catch (\Exception $e) {
            // Fallback to simple dashboard
            return view('company.dashboard-test', [
                'company' => $company,
                'stats' => [
                    'total_jobs' => 0,
                    'active_jobs' => 0,
                    'total_applications' => 0,
                    'pending_applications' => 0,
                ],
                'error' => $e->getMessage()
            ]);
        }
    }

    public function jobs(Request $request)
    {
        $company = Auth::guard('company')->user();
        
        if (!$company) {
            return redirect()->route('login');
        }
        
        $query = Job::where('company_id', $company->id);
        
        // Filter by status (including archived)
        $view = $request->get('view', 'active'); // active, archived, all
        
        switch ($view) {
            case 'archived':
                $query->archived();
                break;
            case 'all':
                // Show all jobs
                break;
            case 'active':
            default:
                $query->active();
                break;
        }
        
        // Get jobs with pagination
        $jobs = $query->withCount('applications')
            ->latest()
            ->paginate(10);
        
        $jobTypes = Job::JOB_TYPES;
        
        // Count for tabs
        $counts = [
            'active' => Job::where('company_id', $company->id)->active()->count(),
            'archived' => Job::where('company_id', $company->id)->archived()->count(),
            'all' => Job::where('company_id', $company->id)->count(),
        ];
        
        return view('company.jobs', compact('jobs', 'jobTypes', 'view', 'counts'));
    }

    public function applications(Request $request)
    {
        $company = Auth::guard('company')->user();
        
        if (!$company) {
            return redirect()->route('login');
        }
        
        $query = Application::with(['alumni', 'job'])
            ->whereHas('job', function($q) use ($company) {
                $q->where('company_id', $company->id);
            });

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by job
        if ($request->filled('job_id')) {
            $query->where('job_posting_id', $request->job_id);
        }

        $applications = $query->latest()->paginate(10);

        // Get jobs for filter
        $jobs = Job::where('company_id', $company->id)
            ->select('id', 'title')
            ->get();

        return view('company.applications', compact('applications', 'jobs'));
    }

    public function createJobForm()
    {
        $company = Auth::guard('company')->user();
        
        if (!$company) {
            return redirect()->route('login');
        }

        $jobTypes = Job::JOB_TYPES;
        
        return view('company.create-job', compact('jobTypes'));
    }

    public function createJob(Request $request)
    {
        $company = Auth::guard('company')->user();
        
        if (!$company) {
            return redirect()->route('login');
        }

        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'requirements' => 'required|string',
                'location' => 'required|string|max:255',
                'job_type' => 'required|in:' . implode(',', array_keys(Job::JOB_TYPES)),
                'salary_min' => 'nullable|numeric|min:0',
                'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
                'application_deadline' => 'required|date|after:today',
                'positions_available' => 'required|integer|min:1',
            ]);

            $job = Job::create([
                'company_id' => $company->id,
                'title' => $request->title,
                'description' => $request->description,
                'requirements' => $request->requirements,
                'location' => $request->location,
                'type' => $request->job_type,
                'salary_min' => $request->salary_min ?: null,
                'salary_max' => $request->salary_max ?: null,
                'application_deadline' => $request->application_deadline,
                'positions_available' => $request->positions_available,
                'status' => 'active',
            ]);

            return redirect()->route('company.dashboard')
                ->with('success', 'Lowongan berhasil dibuat!');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('jobTypes', Job::JOB_TYPES);
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Terjadi kesalahan saat membuat lowongan: ' . $e->getMessage()])
                ->withInput()
                ->with('jobTypes', Job::JOB_TYPES);
        }
    }

    public function profile()
    {
        $company = Auth::guard('company')->user();
        
        if (!$company) {
            return redirect()->route('login');
        }
        
        return view('company.profile', compact('company'));
    }

    public function updateProfile(Request $request)
    {
        $company = Auth::guard('company')->user();
        
        if (!$company) {
            return redirect()->route('login');
        }
        
        $request->validate([
            'company_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'website' => 'nullable|url',
            'industry' => 'required|string|max:255',
            'description' => 'nullable|string',
            'contact_person' => 'required|string|max:255',
            'contact_position' => 'required|string|max:255',
            'logo' => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:1024',
        ]);

        $data = $request->only([
            'company_name', 'phone', 'address', 'website',
            'industry', 'description', 'contact_person', 'contact_position'
        ]);

        if ($request->hasFile('logo')) {
            try {
                $path = $request->file('logo')->store('company_logos', 'public');
                $data['logo'] = basename($path); // store only filename
            } catch (\Exception $e) {
                return back()->with('error', 'Gagal mengunggah logo: ' . $e->getMessage());
            }
        }

        $company->update($data);

        return back()->with('success', 'Profil perusahaan berhasil diperbarui!');
    }

    public function editJobForm($id)
    {
        $company = Auth::guard('company')->user();
        if (!$company) {
            return redirect()->route('login');
        }
        $job = Job::where('company_id', $company->id)->findOrFail($id);
        $jobTypes = Job::JOB_TYPES;
        return view('company.edit-job', compact('job', 'jobTypes'));
    }

    public function updateJob(Request $request, $id)
    {
        $company = Auth::guard('company')->user();
        if (!$company) {
            return redirect()->route('login');
        }

        try {
            $job = Job::where('company_id', $company->id)->findOrFail($id);

            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'requirements' => 'required|string',
                'location' => 'required|string|max:255',
                'job_type' => 'required|in:' . implode(',', array_keys(Job::JOB_TYPES)),
                'salary_min' => 'nullable|numeric|min:0',
                'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
                'application_deadline' => 'required|date|after:today',
                'positions_available' => 'required|integer|min:1',
            ]);

            $job->update([
                'title' => $request->title,
                'description' => $request->description,
                'requirements' => $request->requirements,
                'location' => $request->location,
                'type' => $request->job_type,
                'salary_min' => $request->salary_min ?: null,
                'salary_max' => $request->salary_max ?: null,
                'application_deadline' => $request->application_deadline,
                'positions_available' => $request->positions_available,
            ]);

            return redirect()->route('company.jobs')
                ->with('success', 'Lowongan berhasil diperbarui!');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('jobTypes', Job::JOB_TYPES);
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Terjadi kesalahan saat memperbarui lowongan: ' . $e->getMessage()])
                ->withInput()
                ->with('jobTypes', Job::JOB_TYPES);
        }
    }

    public function closeJob($id)
    {
        $company = Auth::guard('company')->user();
        if (!$company) {
            return redirect()->route('login');
        }

        try {
            $job = Job::where('company_id', $company->id)->findOrFail($id);
            
            if ($job->status === 'closed') {
                return redirect()->back()
                    ->with('error', 'Lowongan sudah ditutup sebelumnya.');
            }
            
            $jobTitle = $job->title;
            $job->update(['status' => 'closed']);

            return redirect()->back()
                ->with('success', "Lowongan '{$jobTitle}' berhasil ditutup!");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menutup lowongan: ' . $e->getMessage());
        }
    }

    public function deleteJob($id)
    {
        $company = Auth::guard('company')->user();
        if (!$company) {
            return redirect()->route('login');
        }

        try {
            $job = Job::where('company_id', $company->id)->findOrFail($id);
            $jobTitle = $job->title;
            $job->delete();

            return redirect()->route('company.jobs')
                ->with('success', "Lowongan '{$jobTitle}' berhasil dihapus!");
        } catch (\Exception $e) {
            return redirect()->route('company.jobs')
                ->with('error', 'Terjadi kesalahan saat menghapus lowongan: ' . $e->getMessage());
        }
    }

    public function updateApplicationStatus(Request $request, $id)
    {
        $company = Auth::guard('company')->user();
        
        if (!$company) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'status' => 'required|in:reviewed,interview,accepted,rejected',
            'notes' => 'nullable|string|max:1000',
            'interview_at' => 'nullable|date',
            'interview_location' => 'nullable|string|max:255',
            'interview_details' => 'nullable|string|max:2000',
            'interview_media' => 'nullable|string|max:100', // e.g., Zoom, Google Meet, Offline
        ]);

        try {
            $application = Application::whereHas('job', function($query) use ($company) {
                $query->where('company_id', $company->id);
            })->findOrFail($id);

            switch ($request->status) {
                case 'reviewed':
                    $application->markAsReviewed();
                    if ($request->filled('notes')) {
                        $application->notes = $request->notes;
                        $application->save();
                    }
                    break;
                case 'interview':
                    $application->interview_media = $request->interview_media;
                    $application->scheduleInterview(
                        $request->interview_at ?? now()->addDays(1),
                        $request->interview_location,
                        $request->interview_details
                    );
                    if ($request->filled('notes')) {
                        $application->notes = $request->notes;
                        $application->save();
                    }
                    break;
                case 'accepted':
                    $application->accept($request->notes);
                    break;
                case 'rejected':
                    $application->reject($request->notes);
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => 'Status lamaran berhasil diperbarui',
                'data' => [
                    'status' => $application->status,
                    'interview_at' => $application->interview_at?->format('d M Y H:i'),
                    'interview_location' => $application->interview_location,
                    'interview_media' => $application->interview_media,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getApplicationDetail($id)
    {
        $company = Auth::guard('company')->user();
        
        if (!$company) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        try {
            $application = Application::with(['alumni.jurusan', 'job.company'])
                                     ->whereHas('job', function($query) use ($company) {
                                         $query->where('company_id', $company->id);
                                     })
                                     ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $application->id,
                    'status' => $application->status,
                    'applied_at' => $application->created_at->format('d M Y H:i'),
                    'cover_letter' => $application->cover_letter,
                    'cv_file' => $application->cv_file,
                    'notes' => $application->notes,
                    'reviewed_at' => $application->reviewed_at ? $application->reviewed_at->format('d M Y H:i') : null,
                    'interview_at' => $application->interview_at ? $application->interview_at->format('d M Y H:i') : null,
                    'interview_location' => $application->interview_location,
                    'interview_details' => $application->interview_details,
                    'interview_media' => $application->interview_media,
                    'alumni' => [
                        'id' => $application->alumni->id,
                        'name' => $application->alumni->nama,
                        'email' => $application->alumni->email,
                        'phone' => $application->alumni->phone,
                        'jurusan' => $application->alumni->jurusan->nama ?? 'Tidak diketahui',
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
                'message' => 'Lamaran tidak ditemukan'
            ], 404);
        }
    }
}
