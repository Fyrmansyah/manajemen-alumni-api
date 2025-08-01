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
        $query = Job::with('company');

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

        $jobs = $query->paginate(15)->withQueryString();
        $companies = Company::where('status', 'active')->orderBy('company_name')->get();

        return view('admin.jobs.index', compact('jobs', 'companies'));
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
}
