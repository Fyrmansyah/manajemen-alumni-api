<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Company;
use Illuminate\Http\Request;

class AdminJobController extends Controller
{
    public function index(Request $request)
    {
        // Get counts for statistics
        $counts = [
            'active' => Job::active()->count(),
            'archived' => Job::archived()->count(),
            'all' => Job::count()
        ];

        $query = Job::with(['company', 'applications']);

        // Filter by view (active, archived, all)
        $view = $request->get('view', 'active');
        switch ($view) {
            case 'archived':
                $query->archived();
                break;
            case 'all':
                // Show all jobs
                break;
            case 'active':
            default:
                $query->active();
                break;
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('company', function($query) use ($search) {
                      $query->where('company_name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by company
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // Filter by job type
        if ($request->filled('job_type')) {
            $query->where('job_type', $request->job_type);
        }

        // Sorting
        switch ($request->get('sort', 'newest')) {
            case 'oldest':
                $query->oldest();
                break;
            case 'title_asc':
                $query->orderBy('title', 'asc');
                break;
            case 'title_desc':
                $query->orderBy('title', 'desc');
                break;
            case 'salary_high':
                $query->orderBy('salary_max', 'desc');
                break;
            case 'salary_low':
                $query->orderBy('salary_min', 'asc');
                break;
            default:
                $query->latest();
        }

        // Add applications count for statistics
        $query->withCount('applications');

        $jobs = $query->paginate(15)->withQueryString();
        $companies = Company::where('status', 'active')->orderBy('company_name')->get();

        return view('admin.jobs.index', compact('jobs', 'companies', 'view', 'counts'));
    }

    public function create()
    {
        $companies = Company::where('status', 'active')->orderBy('company_name')->get();
        return view('admin.jobs.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'job_type' => 'required|in:full_time,part_time,contract,internship',
            'location' => 'required|string|max:255',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'deadline' => 'required|date|after:today',
            'status' => 'required|in:draft,published,closed',
            'benefits' => 'nullable|string',
            'skills_required' => 'nullable|string',
            'experience_level' => 'nullable|in:entry,junior,mid,senior,lead',
        ]);

        $job = Job::create($validatedData);

        return redirect()
            ->route('admin.jobs.index')
            ->with('success', 'Lowongan kerja berhasil dibuat.');
    }

    public function show(Job $job)
    {
        $job->load(['company', 'applications.alumni']);
        return view('admin.jobs.show', compact('job'));
    }

    public function edit(Job $job)
    {
        $companies = Company::where('status', 'active')->orderBy('company_name')->get();
        return view('admin.jobs.edit', compact('job', 'companies'));
    }

    public function update(Request $request, Job $job)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'job_type' => 'required|in:full_time,part_time,contract,internship',
            'location' => 'required|string|max:255',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'deadline' => 'required|date',
            'status' => 'required|in:draft,published,closed',
            'benefits' => 'nullable|string',
            'skills_required' => 'nullable|string',
            'experience_level' => 'nullable|in:entry,junior,mid,senior,lead',
        ]);

        $job->update($validatedData);

        return redirect()
            ->route('admin.jobs.index')
            ->with('success', 'Lowongan kerja berhasil diperbarui.');
    }

    public function destroy(Job $job)
    {
        try {
            $job->delete();
            return redirect()
                ->route('admin.jobs.index')
                ->with('success', 'Lowongan kerja berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menghapus lowongan kerja.');
        }
    }

    public function archive(Job $job, Request $request)
    {
        try {
            // Log for debugging
            \Log::info('Attempting to archive job', [
                'job_id' => $job->id,
                'job_title' => $job->title,
                'reason' => $request->input('reason'),
                'is_archived_before' => $job->isArchived()
            ]);

            // Validate that job is not already archived
            if ($job->isArchived()) {
                return back()->with('error', 'Lowongan kerja ini sudah diarsipkan.');
            }

            $reason = $request->input('reason', 'Archived by admin');
            $job->archive($reason);
            
            // Log success
            \Log::info('Job archived successfully', [
                'job_id' => $job->id,
                'archived_at' => $job->archived_at,
                'archive_reason' => $job->archive_reason
            ]);
            
            return redirect()
                ->route('admin.jobs.index')
                ->with('success', "Lowongan '{$job->title}' berhasil diarsipkan.");
        } catch (\Exception $e) {
            \Log::error('Failed to archive job', [
                'job_id' => $job->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Terjadi kesalahan saat mengarsipkan lowongan kerja: ' . $e->getMessage());
        }
    }

    public function reactivate(Job $job)
    {
        try {
            if (!$job->isArchived()) {
                return back()->with('error', 'Lowongan ini tidak dalam status arsip.');
            }

            $job->unarchive();
            
            return redirect()
                ->route('admin.jobs.index')
                ->with('success', "Lowongan '{$job->title}' berhasil diaktifkan kembali.");
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat mengaktifkan kembali lowongan kerja.');
        }
    }

    public function bulkArchive(Request $request)
    {
        try {
            $jobIds = $request->input('job_ids', []);
            $reason = $request->input('reason', 'Bulk archived by admin');
            
            if (empty($jobIds)) {
                return back()->with('error', 'Pilih minimal satu lowongan untuk diarsipkan.');
            }

            $jobs = Job::whereIn('id', $jobIds)->whereNull('archived_at')->get();
            
            foreach ($jobs as $job) {
                $job->archive($reason);
            }
            
            return back()->with('success', "Berhasil mengarsipkan {$jobs->count()} lowongan.");
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat mengarsipkan lowongan kerja.');
        }
    }

    public function bulkReactivate(Request $request)
    {
        try {
            $jobIds = $request->input('job_ids', []);
            
            if (empty($jobIds)) {
                return back()->with('error', 'Pilih minimal satu lowongan untuk diaktifkan kembali.');
            }

            $jobs = Job::whereIn('id', $jobIds)->whereNotNull('archived_at')->get();
            
            foreach ($jobs as $job) {
                $job->unarchive();
            }
            
            return back()->with('success', "Berhasil mengaktifkan kembali {$jobs->count()} lowongan.");
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat mengaktifkan kembali lowongan kerja.');
        }
    }
}
