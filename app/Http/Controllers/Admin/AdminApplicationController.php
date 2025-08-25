<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Job;
use Illuminate\Http\Request;

class AdminApplicationController extends Controller
{
    public function index(Request $request)
    {
        $query = Application::with(['alumni', 'job.company']);

        // Filtering
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('job_id')) {
            $query->where('job_id', $request->job_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('alumni', function ($qa) use ($search) {
                    $qa->where('nama', 'like', "%{$search}%")
                        ->orWhere('nama_lengkap', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('job', function ($qj) use ($search) {
                    $qj->where('title', 'like', "%{$search}%");
                })->orWhereHas('job.company', function ($qc) use ($search) {
                    $qc->where('name', 'like', "%{$search}%");
                });
            });
        }

        // Sorting (default newest by applied_at / created_at)
        if ($request->get('sort') === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $applications = $query->paginate(15)->appends($request->query());
        $jobs = Job::orderBy('title')->get(['id', 'title']);

        return view('admin.applications.index', compact('applications', 'jobs'));
    }

    public function show(Application $application)
    {
        $application->load(['alumni', 'job.company']);
        return view('admin.applications.show', compact('application'));
    }

    public function updateStatus(Request $request, Application $application)
    {
        $request->validate([
            'status' => 'required|in:submitted,reviewed,interview,accepted,rejected',
            'notes' => 'nullable|string|max:1000',
            'interview_at' => 'nullable|date',
            'interview_location' => 'nullable|string|max:255',
            'interview_details' => 'nullable|string|max:2000',
        ]);

        switch ($request->status) {
            case 'reviewed':
                $application->markAsReviewed();
                if ($request->filled('notes')) {
                    $application->notes = $request->notes;
                    $application->save();
                }
                break;
            case 'interview':
                $application->scheduleInterview(
                    $request->interview_at ?? now(),
                    $request->interview_location,
                    $request->interview_details
                );
                break;
            case 'accepted':
                $application->accept($request->notes);
                break;
            case 'rejected':
                $application->reject($request->notes);
                break;
            case 'submitted':
            default:
                $application->update(['status' => $request->status]);
        }

        return back()->with('success', 'Status lamaran berhasil diperbarui');
    }
}
