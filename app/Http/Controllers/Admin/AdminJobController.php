<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Company;
use Illuminate\Http\Request;

class AdminJobController extends Controller
{
    /**
     * Auto archive expired jobs
     */
    private function autoArchiveExpiredJobs()
    {
        try {
            $expiredJobs = Job::where('status', 'active')
                ->where('application_deadline', '<', now())
                ->whereNull('archived_at')
                ->get();

            foreach ($expiredJobs as $job) {
                $job->archive('Auto-archived: Application deadline expired');
            }

            if ($expiredJobs->count() > 0) {
                \Log::info("Auto-archived {$expiredJobs->count()} expired jobs", [
                    'job_ids' => $expiredJobs->pluck('id')->toArray()
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to auto-archive expired jobs: ' . $e->getMessage());
        }
    }
    
    public function index(Request $request)
    {
        // Auto-archive expired jobs before showing the list
        $this->autoArchiveExpiredJobs();

        // Get counts for statistics (accurate counts excluding archived jobs from active)
        $counts = [
            'active' => Job::active()->count(), // This uses the scope that excludes archived
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
                // Show all jobs (no additional filtering)
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
            $query->where('type', $request->job_type);
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
        $companies = Company::where('status', 'aktif')
                           ->where(function($query) {
                               $query->where('is_verified', true)
                                     ->orWhere('is_approved', true);
                           })
                           ->orderBy('company_name')
                           ->get(['id', 'company_name']);

        return view('admin.jobs.index', compact('jobs', 'companies', 'view', 'counts'));
    }

    public function create()
    {
        $companies = Company::where('status', 'aktif')
                           ->where(function($query) {
                               $query->where('is_verified', true)
                                     ->orWhere('is_approved', true);
                           })
                           ->orderBy('company_name')
                           ->get(['id', 'company_name', 'status', 'is_verified', 'is_approved']);
        return view('admin.jobs.create', compact('companies'));
    }

    public function store(Request $request)
    {
                $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'type' => 'required|in:full_time,part_time,contract,freelance,internship',
            'location' => 'required|string|max:255',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'application_deadline' => 'required|date',
            'status' => 'required|in:draft,active,closed',
            'positions_available' => 'required|integer|min:1',
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
            'type' => 'required|in:full_time,part_time,contract,freelance,internship',
            'location' => 'required|string|max:255',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'application_deadline' => 'required|date',
            'status' => 'required|in:draft,active,closed',
            'positions_available' => 'required|integer|min:1',
        ]);

        $job->update($validatedData);

        return redirect()
            ->route('admin.jobs.index')
            ->with('success', 'Lowongan kerja berhasil diperbarui.');
    }

    public function destroy(Job $job, Request $request)
    {
        try {
            // Get reason from request
            $reason = $request->input('reason', 'Deleted by admin');
            
            // Gunakan pengarsipan sebagai penghapusan yang aman (soft delete via archive)
            if ($job->isArchived()) {
                // Jika sudah diarsip, tidak perlu menghapus lagi
                return redirect()
                    ->route('admin.jobs.index')
                    ->with('info', "Lowongan '{$job->title}' sudah dalam status arsip.");
            }

            $job->archive($reason);
            return redirect()
                ->route('admin.jobs.index')
                ->with('success', "Lowongan '{$job->title}' dipindahkan ke arsip. Alasan: {$reason}");
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

            // Jika deadline telah lewat, otomatis perpanjang agar tidak langsung terarsip lagi
            $extended = false;
            if ($job->application_deadline && $job->application_deadline < now()) {
                $job->application_deadline = now()->addDays(30); // perpanjang 30 hari
                $extended = true;
            }

            // Unarchive dan aktifkan kembali
            $job->unarchive();

            // Simpan perubahan deadline jika ada
            if ($extended) {
                $job->save();
            }

            $message = "Lowongan '{$job->title}' berhasil diaktifkan kembali.";
            if ($extended) {
                $message .= ' Deadline lamaran telah diperpanjang 30 hari ke depan.';
            }

            return redirect()
                ->route('admin.jobs.index')
                ->with('success', $message);
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

            $extendedCount = 0;
            foreach ($jobs as $job) {
                // Perpanjang deadline jika sudah lewat agar tidak langsung diarsip ulang
                if ($job->application_deadline && $job->application_deadline < now()) {
                    $job->application_deadline = now()->addDays(30);
                    $extendedCount++;
                }

                $job->unarchive();
                // Simpan jika ada perubahan deadline
                $job->save();
            }

            $baseMsg = "Berhasil mengaktifkan kembali {$jobs->count()} lowongan.";
            if ($extendedCount > 0) {
                $baseMsg .= " {$extendedCount} lowongan diperpanjang deadlinenya 30 hari.";
            }

            return back()->with('success', $baseMsg);
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat mengaktifkan kembali lowongan kerja.');
        }
    }
}
