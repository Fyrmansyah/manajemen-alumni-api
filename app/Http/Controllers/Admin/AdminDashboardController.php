<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use App\Models\Company;
use App\Models\Job;
use App\Models\Application;
use App\Models\News;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Get statistics
        $stats = [
            'total_alumni' => Alumni::count(),
            'total_companies' => Company::where('status', 'aktif')->count(),
            'active_jobs' => Job::where('status', 'active')->count(),
            'total_applications' => Application::count(),
        ];

        // Get recent activities (mock data for now)
        $recentActivities = [
            [
                'title' => 'Perusahaan Baru Terdaftar',
                'description' => 'PT Teknologi Maju mendaftar sebagai partner',
                'time' => '2 jam lalu',
                'icon' => 'building',
                'color' => 'success'
            ],
            [
                'title' => 'Lamaran Baru',
                'description' => 'Ahmad Rizki melamar sebagai Web Developer',
                'time' => '3 jam lalu',
                'icon' => 'file-alt',
                'color' => 'info'
            ],
            [
                'title' => 'Lowongan Diterbitkan',
                'description' => 'PT Digital Solutions posting lowongan UI/UX Designer',
                'time' => '5 jam lalu',
                'icon' => 'briefcase',
                'color' => 'primary'
            ],
            [
                'title' => 'Berita Dipublikasi',
                'description' => 'Tips Interview Kerja untuk Fresh Graduate',
                'time' => '1 hari lalu',
                'icon' => 'newspaper',
                'color' => 'warning'
            ]
        ];

        // Get chart data for applications per month (last 12 months)
        $chartData = $this->getApplicationsChartData();

        // Get top companies by job count
        $topCompanies = Company::withCount(['jobs' => function($query) {
                $query->where('status', 'active');
            }])
            ->where('status', 'verified')
            ->orderBy('jobs_count', 'desc')
            ->take(5)
            ->get();

        // Get recent jobs
        $recentJobs = Job::with('company')
            ->where('status', 'active')
            ->latest()
            ->take(5)
            ->get();

        // Get recent applications
        $recentApplications = Application::with(['alumni', 'job.company'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentActivities',
            'chartData',
            'topCompanies',
            'recentJobs',
            'recentApplications'
        ));
    }

    private function getApplicationsChartData()
    {
        $months = [];
        $applications = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M Y');
            
            $count = Application::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $applications[] = $count;
        }

        return [
            'months' => $months,
            'applications' => $applications
        ];
    }
}
