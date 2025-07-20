<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;

class AdminApplicationController extends Controller
{
    public function index()
    {
        $applications = Application::with(['alumni', 'job.company'])
            ->latest()
            ->paginate(15);
        return view('admin.applications.index', compact('applications'));
    }

    public function show(Application $application)
    {
        $application->load(['alumni', 'job.company']);
        return view('admin.applications.show', compact('application'));
    }

    public function updateStatus(Request $request, Application $application)
    {
        $request->validate([
            'status' => 'required|in:pending,accepted,rejected'
        ]);

        $application->update([
            'status' => $request->status
        ]);

        return back()->with('success', 'Status lamaran berhasil diperbarui');
    }
}
