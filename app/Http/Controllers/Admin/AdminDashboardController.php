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
            'total_companies' => Company::where('status', 'active')->count(),
            'active_jobs' => Job::where('status', 'published')->count(),
            'total_applications' => Application::count(),
        ];

        // Get real recent activities
        $recentActivities = $this->getRecentActivities();

        // Get chart data for applications per month (last 12 months)
        $chartData = $this->getApplicationsChartData();

        // Get top companies by job count
        $topCompanies = Company::withCount(['jobs' => function($query) {
                $query->where('status', 'published');
            }])
            ->where('status', 'active')
            ->orderBy('jobs_count', 'desc')
            ->take(5)
            ->get();

        // Get recent jobs
        $recentJobs = Job::with('company')
            ->where('status', 'published')
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

    public function refreshActivities()
    {
        $activities = $this->getRecentActivities();
        
        // Set timezone untuk Indonesia
        Carbon::setLocale('id');
        $now = Carbon::now('Asia/Jakarta');
        
        return response()->json([
            'success' => true,
            'activities' => $activities,
            'timestamp' => $now->format('H:i:s') . ' WIB'
        ]);
    }

    private function getRecentActivities()
    {
        $activities = collect();
        
        // Set timezone untuk Indonesia
        Carbon::setLocale('id');
        $dayAgo = Carbon::now('Asia/Jakarta')->subDay();

        // Get recent companies (last 24 hours)
        $recentCompanies = Company::where('created_at', '>=', $dayAgo)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($recentCompanies as $company) {
            $createdAt = Carbon::parse($company->created_at)->setTimezone('Asia/Jakarta');
            $activities->push([
                'title' => 'Perusahaan Baru Terdaftar',
                'description' => $company->company_name . ' mendaftar sebagai partner',
                'time' => $createdAt->diffForHumans() . ' WIB',
                'icon' => 'building',
                'color' => 'success',
                'timestamp' => $createdAt
            ]);
        }

        // Get recent applications (last 24 hours)
        $recentApplications = Application::with(['alumni', 'job'])
            ->where('created_at', '>=', $dayAgo)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($recentApplications as $application) {
            $createdAt = Carbon::parse($application->created_at)->setTimezone('Asia/Jakarta');
            $activities->push([
                'title' => 'Lamaran Baru',
                'description' => ($application->alumni->nama_lengkap ?? 'Alumni') . ' melamar sebagai ' . ($application->job->title ?? 'Posisi'),
                'time' => $createdAt->diffForHumans() . ' WIB',
                'icon' => 'file-alt',
                'color' => 'info',
                'timestamp' => $createdAt
            ]);
        }

        // Get recent jobs (last 24 hours)
        $recentJobs = Job::with('company')
            ->where('created_at', '>=', $dayAgo)
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($recentJobs as $job) {
            $createdAt = Carbon::parse($job->created_at)->setTimezone('Asia/Jakarta');
            $activities->push([
                'title' => 'Lowongan Diterbitkan',
                'description' => ($job->company->company_name ?? 'Perusahaan') . ' posting lowongan ' . $job->title,
                'time' => $createdAt->diffForHumans() . ' WIB',
                'icon' => 'briefcase',
                'color' => 'primary',
                'timestamp' => $createdAt
            ]);
        }

        // Get recent news (last 24 hours)
        $recentNews = News::where('created_at', '>=', $dayAgo)
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($recentNews as $news) {
            $createdAt = Carbon::parse($news->created_at)->setTimezone('Asia/Jakarta');
            $activities->push([
                'title' => 'Berita Dipublikasi',
                'description' => $news->title,
                'time' => $createdAt->diffForHumans() . ' WIB',
                'icon' => 'newspaper',
                'color' => 'warning',
                'timestamp' => $createdAt
            ]);
        }

        // Get recent alumni registrations (last 24 hours)
        $recentAlumni = Alumni::with('jurusan')
            ->where('created_at', '>=', $dayAgo)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($recentAlumni as $alumni) {
            $createdAt = Carbon::parse($alumni->created_at)->setTimezone('Asia/Jakarta');
            $activities->push([
                'title' => 'Alumni Baru Terdaftar',
                'description' => $alumni->nama_lengkap . ' dari jurusan ' . ($alumni->jurusan->nama_jurusan ?? 'Tidak diketahui'),
                'time' => $createdAt->diffForHumans() . ' WIB',
                'icon' => 'user-graduate',
                'color' => 'info',
                'timestamp' => $createdAt
            ]);
        }

        // Sort by timestamp descending and take 10 most recent
        return $activities->sortByDesc('timestamp')->take(10)->values();
    }
}
