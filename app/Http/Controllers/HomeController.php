<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\News;
use App\Models\Alumni;
use App\Models\Company;
use App\Models\Application;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Get statistics
        $stats = [
            'total_jobs' => Job::where('status', 'active')->count(),
            'total_companies' => Company::where('status', 'verified')->count(),
            'total_alumni' => Alumni::count(),
            'total_applications' => Application::count(),
        ];

        // Get latest jobs (limit 6)
        $latest_jobs = Job::with('company')
            ->where('status', 'active')
            ->latest()
            ->take(6)
            ->get();

        // Get latest news (limit 6)
        $latest_news = News::where('status', 'published')
            ->latest()
            ->take(6)
            ->get();

        return view('home', compact('stats', 'latest_jobs', 'latest_news'));
    }
}
