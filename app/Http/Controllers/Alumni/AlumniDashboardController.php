<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlumniDashboardController extends Controller
{
    public function index()
    {
        $alumni = Auth::guard('alumni')->user(); // Use alumni guard
        
        if (!$alumni) {
            return redirect()->route('login');
        }
        
        // Get statistics
        $stats = [
            'total_applications' => Application::where('alumni_id', $alumni->id)->count(),
            'pending_applications' => Application::where('alumni_id', $alumni->id)->where('status', 'submitted')->count(),
            'accepted_applications' => Application::where('alumni_id', $alumni->id)->where('status', 'accepted')->count(),
            'available_jobs' => Job::where('status', 'active')->count(),
        ];

        // Get recent applications
        $recentApplications = Application::with(['job.company'])
            ->where('alumni_id', $alumni->id)
            ->latest()
            ->take(5)
            ->get();

        // Get recommended jobs (based on graduation year, major, etc.)
        $recommendedJobs = Job::with('company')
            ->where('status', 'active')
            ->latest()
            ->take(6)
            ->get();

        return view('alumni.dashboard', compact('stats', 'recentApplications', 'recommendedJobs'));
    }

    public function applications(Request $request)
    {
        $alumni = Auth::guard('alumni')->user();
        
        if (!$alumni) {
            return redirect()->route('login');
        }
        
        $query = Application::with(['job.company'])
            ->where('alumni_id', $alumni->id);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $applications = $query->latest()->paginate(10);

        return view('alumni.applications', compact('applications'));
    }

    public function profile()
    {
        $alumni = Auth::guard('alumni')->user();
        
        if (!$alumni) {
            return redirect()->route('login');
        }
        
        return view('alumni.profile', compact('alumni'));
    }

    public function updateProfile(Request $request)
    {
        $alumni = Auth::guard('alumni')->user();
        
        if (!$alumni) {
            return redirect()->route('login');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'current_job' => 'nullable|string|max:255',
            'current_company' => 'nullable|string|max:255',
            'linkedin_url' => 'nullable|url',
            'skills' => 'nullable|string',
        ]);

        $alumni->update($request->only([
            'name', 'phone', 'address', 'current_job', 
            'current_company', 'linkedin_url', 'skills'
        ]));

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}
