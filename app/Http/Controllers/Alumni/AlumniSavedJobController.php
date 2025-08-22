<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AlumniSavedJobController extends Controller
{
    public function index(Request $request)
    {
        $alumni = $request->user('alumni');

        $query = $alumni->savedJobs()->with(['company']);

        if ($search = $request->query('q')) {
            $query->where(function($q) use ($search) {
                $q->where('title','like',"%{$search}%")
                  ->orWhereHas('company', function($c) use ($search){
                      $c->where('company_name','like',"%{$search}%");
                  });
            });
        }

        $jobs = $query->latest('saved_jobs.created_at')->paginate(10)->withQueryString();

        return view('alumni.saved-jobs', compact('jobs'));
    }
}
