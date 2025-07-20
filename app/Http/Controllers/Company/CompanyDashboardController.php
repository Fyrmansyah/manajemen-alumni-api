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
        // Ensure company user is properly set in the guard
        if (session('company_logged_in') && session('company_id')) {
            $company = \App\Models\Company::find(session('company_id'));
            if ($company) {
                \Illuminate\Support\Facades\Auth::guard('company')->setUser($company);
            }
        }
        
        $company = Auth::guard('company')->user();
        
        if (!$company) {
            return redirect()->route('login');
        }
        
        // Get statistics
        $stats = [
            'total_jobs' => Job::where('company_id', $company->id)->count(),
            'active_jobs' => Job::where('company_id', $company->id)->where('status', 'active')->count(),
            'total_applications' => Application::whereHas('job', function($query) use ($company) {
                $query->where('company_id', $company->id);
            })->count(),
            'pending_applications' => Application::whereHas('job', function($query) use ($company) {
                $query->where('company_id', $company->id);
            })->where('status', 'pending')->count(),
        ];

        // Get recent jobs
        $recentJobs = Job::where('company_id', $company->id)
            ->latest()
            ->take(5)
            ->get();

        // Get recent applications
        $recentApplications = Application::with(['alumni', 'job'])
            ->whereHas('job', function($query) use ($company) {
                $query->where('company_id', $company->id);
            })
            ->latest()
            ->take(5)
            ->get();

        return view('company.dashboard', compact('stats', 'recentJobs', 'recentApplications'));
    }

    public function jobs(Request $request)
    {
        $company = Auth::guard('company')->user();
        
        if (!$company) {
            return redirect()->route('login');
        }
        
        $query = Job::where('company_id', $company->id);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $jobs = $query->latest()->paginate(10);

        return view('company.jobs', compact('jobs'));
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
        ]);

        $company->update($request->only([
            'company_name', 'phone', 'address', 'website',
            'industry', 'description', 'contact_person', 'contact_position'
        ]));

        return back()->with('success', 'Profil perusahaan berhasil diperbarui!');
    }
}
