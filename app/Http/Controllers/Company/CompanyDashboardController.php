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
