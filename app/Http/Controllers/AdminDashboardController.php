<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Alumni;
use App\Models\Company;
use App\Models\Job;
use App\Models\Application;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Get statistics
        $stats = [
            'total_alumni' => Alumni::count(),
            'total_companies' => Company::count(),
            'total_jobs' => Job::count(),
            'total_applications' => Application::count(),
            'pending_applications' => Application::where('status', 'submitted')->count(),
            'accepted_applications' => Application::where('status', 'accepted')->count(),
        ];

        // Get recent activities (real-time data)
        $recentActivities = $this->getRecentActivities();

        // Get monthly statistics for charts
        $monthlyStats = $this->getMonthlyStats();

        // Get job statistics
        $jobStats = [
            'active_jobs' => Job::where('status', 'active')->count(),
            'closed_jobs' => Job::where('status', 'closed')->count(),
            'applications_this_month' => Application::whereMonth('created_at', now()->month)->count(),
        ];

        return view('admin.dashboard', compact('stats', 'recentActivities', 'monthlyStats', 'jobStats'));
    }

    private function getRecentActivities()
    {
        $activities = collect();

        // Recent applications
        $applications = Application::with(['alumni', 'job'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($application) {
                return [
                    'type' => 'application',
                    'title' => 'Lamaran Baru',
                    'description' => ($application->alumni->nama ?? 'Alumni') . ' melamar untuk ' . $application->job->title,
                    'time' => $application->created_at,
                    'icon' => 'fas fa-file-alt',
                    'color' => 'primary'
                ];
            });

        // Recent company registrations
        $companies = Company::latest()
            ->take(3)
            ->get()
            ->map(function ($company) {
                return [
                    'type' => 'company',
                    'title' => 'Perusahaan Baru',
                    'description' => $company->company_name . ' bergabung',
                    'time' => $company->created_at,
                    'icon' => 'fas fa-building',
                    'color' => 'success'
                ];
            });

        // Recent job postings
        $jobs = Job::with('company')
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($job) {
                return [
                    'type' => 'job',
                    'title' => 'Lowongan Baru',
                    'description' => $job->title . ' dari ' . ($job->company->company_name ?? 'Perusahaan'),
                    'time' => $job->created_at,
                    'icon' => 'fas fa-briefcase',
                    'color' => 'info'
                ];
            });

        // Recent news
        $news = News::latest()
            ->take(2)
            ->get()
            ->map(function ($newsItem) {
                return [
                    'type' => 'news',
                    'title' => 'Berita Baru',
                    'description' => $newsItem->title,
                    'time' => $newsItem->created_at,
                    'icon' => 'fas fa-newspaper',
                    'color' => 'warning'
                ];
            });

        // Merge and sort by time
        $activities = $activities
            ->merge($applications)
            ->merge($companies)
            ->merge($jobs)
            ->merge($news)
            ->sortByDesc('time')
            ->take(10);

        return $activities;
    }

    private function getMonthlyStats()
    {
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months->push([
                'month' => $date->format('M Y'),
                'alumni' => Alumni::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'companies' => Company::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'jobs' => Job::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'applications' => Application::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ]);
        }

        return $months;
    }

    public function refreshActivities()
    {
        $activities = $this->getRecentActivities();
        
        return response()->json([
            'success' => true,
            'data' => $activities->map(function ($activity) {
                return [
                    'type' => $activity['type'],
                    'title' => $activity['title'],
                    'description' => $activity['description'],
                    'time' => $activity['time']->diffForHumans(),
                    'icon' => $activity['icon'],
                    'color' => $activity['color']
                ];
            })
        ]);
    }
}
