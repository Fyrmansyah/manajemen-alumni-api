<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Company;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $query = Job::with('company')
                   ->active();

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by location
        if ($request->filled('location')) {
            $query->byLocation($request->location);
        }

        // Filter by job type
        if ($request->filled('job_type')) {
            $query->byJobType($request->job_type);
        }

        // Filter by company category
        if ($request->filled('category')) {
            $query->whereHas('company', function ($q) use ($request) {
                $q->where('category_id', $request->category);
            });
        }

        $jobs = $query->latest()->paginate(10);

        // Get filter options
        $locations = Job::active()
                       ->distinct()
                       ->pluck('location')
                       ->filter()
                       ->sort()
                       ->values();

        $jobTypes = Job::JOB_TYPES;

        return response()->json([
            'success' => true,
            'data' => [
                'jobs' => $jobs,
                'locations' => $locations,
                'job_types' => $jobTypes,
                'total_jobs' => Job::active()->count(),
            ]
        ]);
    }

    public function show($id)
    {
        $job = Job::with(['company', 'applications' => function ($query) {
                        $query->where('alumni_id', Auth::id());
                    }])
                   ->findOrFail($id);

        // Increment views
        $job->incrementViews();

        $hasApplied = false;
        if (Auth::check() && Auth::user() instanceof \App\Models\Alumni) {
            $hasApplied = Auth::user()->hasAppliedFor($job);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'job' => $job,
                'has_applied' => $hasApplied,
                'can_apply' => $job->canApply() && !$hasApplied,
            ]
        ]);
    }

    public function apply(Request $request, $id)
    {
        $request->validate([
            'cover_letter' => 'required|string|max:2000',
            'cv_file' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        $job = Job::findOrFail($id);
        $alumni = Auth::user();

        // Check if already applied
        if ($alumni->hasAppliedFor($job)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melamar untuk posisi ini'
            ], 422);
        }

        // Check if job is still available
        if (!$job->canApply()) {
            return response()->json([
                'success' => false,
                'message' => 'Lowongan ini sudah tidak tersedia'
            ], 422);
        }

        $applicationData = [
            'alumni_id' => $alumni->id,
            'job_posting_id' => $job->id,
            'cover_letter' => $request->cover_letter,
            'applied_at' => now(),
        ];

        // Handle CV upload
        if ($request->hasFile('cv_file')) {
            $cvFile = $request->file('cv_file');
            $filename = time() . '_' . $alumni->id . '.' . $cvFile->getClientOriginalExtension();
            $cvFile->storeAs('cvs', $filename, 'public');
            $applicationData['cv_file'] = $filename;
        }

        Application::create($applicationData);

        return response()->json([
            'success' => true,
            'message' => 'Lamaran berhasil dikirim'
        ]);
    }

    public function myApplications()
    {
        $alumni = Auth::user();
        
        $applications = Application::with(['job.company'])
                                  ->forAlumni($alumni->id)
                                  ->latest('applied_at')
                                  ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $applications
        ]);
    }

    // Web view methods
    public function indexWeb(Request $request)
    {
        $query = Job::with('company')->where('status', 'active');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('company', function($company) use ($search) {
                      $company->where('company_name', 'like', "%{$search}%");
                  });
            });
        }

        // Location filter
        if ($request->filled('location')) {
            $query->where('location', $request->get('location'));
        }

        // Job type filter
        if ($request->filled('type')) {
            $query->where('type', $request->get('type'));
        }

        // Salary filter
        if ($request->filled('salary_min')) {
            $query->where('salary_min', '>=', $request->get('salary_min'));
        }

        // Sorting
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'salary_high':
                $query->orderBy('salary_max', 'desc');
                break;
            case 'salary_low':
                $query->orderBy('salary_min', 'asc');
                break;
            default:
                $query->latest();
                break;
        }

        $jobs = $query->paginate(12)->withQueryString();

        // Get filter options
        $locations = Job::where('status', 'active')
            ->distinct()
            ->pluck('location')
            ->filter()
            ->sort();

        $job_types = [
            'full_time' => 'Full Time',
            'part_time' => 'Part Time',
            'contract' => 'Contract',
            'internship' => 'Internship',
            'freelance' => 'Freelance'
        ];

        return view('jobs.index', compact('jobs', 'locations', 'job_types'));
    }

    public function showWeb(Job $job)
    {
        $job->load('company');

        // Get related jobs (same company or similar position)
        $relatedJobs = Job::with('company')
            ->where('status', 'active')
            ->where('id', '!=', $job->id)
            ->where(function($query) use ($job) {
                $query->where('company_id', $job->company_id)
                      ->orWhere('title', 'like', '%' . $job->title . '%');
            })
            ->take(4)
            ->get();

        // Check if current user has applied
        $hasApplied = false;
        if (auth()->check() && auth()->user()->role === 'alumni') {
            $hasApplied = $job->applications()
                ->where('alumni_id', auth()->user()->alumni->id)
                ->exists();
        }

        return view('jobs.show', compact('job', 'relatedJobs', 'hasApplied'));
    }
}
