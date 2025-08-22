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
