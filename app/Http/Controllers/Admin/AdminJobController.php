<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;

class AdminJobController extends Controller
{
    public function index()
    {
        $jobs = Job::with('company')->latest()->paginate(15);
        return view('admin.jobs.index', compact('jobs'));
    }

    public function create()
    {
        return view('admin.jobs.create');
    }

    public function store(Request $request)
    {
        // Implement job creation logic
        return redirect()->route('admin.jobs.index')->with('success', 'Lowongan berhasil dibuat');
    }

    public function show(Job $job)
    {
        $job->load('company');
        return view('admin.jobs.show', compact('job'));
    }

    public function edit(Job $job)
    {
        $job->load('company');
        return view('admin.jobs.edit', compact('job'));
    }

    public function update(Request $request, Job $job)
    {
        // Implement job update logic
        return redirect()->route('admin.jobs.index')->with('success', 'Lowongan berhasil diperbarui');
    }

    public function destroy(Job $job)
    {
        $job->delete();
        return redirect()->route('admin.jobs.index')->with('success', 'Lowongan berhasil dihapus');
    }
}
