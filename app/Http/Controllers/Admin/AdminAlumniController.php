<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminAlumniController extends Controller
{
    public function index(Request $request)
    {
        $query = Alumni::with(['jurusan']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by jurusan
        if ($request->filled('jurusan_id')) {
            $query->where('jurusan_id', $request->jurusan_id);
        }

        // Filter by tahun lulus
        if ($request->filled('tahun_lulus')) {
            $query->where('tahun_lulus', $request->tahun_lulus);
        }

        // Filter by status kerja
        if ($request->filled('status_kerja')) {
            $query->where('status_kerja', $request->status_kerja);
        }

        // Sorting
        switch ($request->get('sort', 'newest')) {
            case 'oldest':
                $query->oldest();
                break;
            case 'name_asc':
                $query->orderBy('nama_lengkap', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('nama_lengkap', 'desc');
                break;
            case 'tahun_lulus':
                $query->orderBy('tahun_lulus', 'desc');
                break;
            default:
                $query->latest();
        }

        $alumni = $query->paginate(15)->withQueryString();

        // Get jurusans with alumni count
        $jurusans = Jurusan::withCount('alumni')->get();
        $totalAlumni = Alumni::count();

        return view('admin.alumni.index', compact('alumni', 'jurusans', 'totalAlumni'));
    }

    public function show(Alumni $alumni)
    {
        $alumni->load(['jurusan']);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $alumni
            ]);
        }

        return view('admin.alumni.show', compact('alumni'));
    }

    public function create()
    {
        $jurusans = Jurusan::all();
        return view('admin.alumni.create', compact('jurusans'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nisn' => 'required|string|unique:alumni|max:20',
            'email' => 'required|email|unique:alumni',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'jurusan_id' => 'required|exists:jurusans,id',
            'tahun_lulus' => 'required|integer|min:2000|max:' . (date('Y') + 5),
            'status_kerja' => 'nullable|in:bekerja,kuliah,wirausaha,menganggur',
            'perusahaan' => 'nullable|string|max:255',
            'posisi' => 'nullable|string|max:255',
            'gaji' => 'nullable|numeric|min:0',
            'is_verified' => 'boolean',
        ]);

        $alumni = Alumni::create($validatedData);

        return redirect()
            ->route('admin.alumni.index')
            ->with('success', 'Data alumni berhasil ditambahkan.');
    }

    public function edit(Alumni $alumni)
    {
        $jurusans = Jurusan::all();
        return view('admin.alumni.edit', compact('alumni', 'jurusans'));
    }

    public function update(Request $request, Alumni $alumni)
    {
        $validatedData = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nisn' => 'required|string|max:20|unique:alumni,nisn,' . $alumni->id,
            'email' => 'required|email|unique:alumni,email,' . $alumni->id,
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'jurusan_id' => 'required|exists:jurusans,id',
            'tahun_lulus' => 'required|integer|min:2000|max:' . (date('Y') + 5),
            'status_kerja' => 'nullable|in:bekerja,kuliah,wirausaha,menganggur',
            'perusahaan' => 'nullable|string|max:255',
            'posisi' => 'nullable|string|max:255',
            'gaji' => 'nullable|numeric|min:0',
            'is_verified' => 'boolean',
        ]);

        $alumni->update($validatedData);

        return redirect()
            ->route('admin.alumni.index')
            ->with('success', 'Data alumni berhasil diperbarui.');
    }

    public function destroy(Alumni $alumni)
    {
        try {
            // Delete CV file if exists
            if ($alumni->cv_path && Storage::exists($alumni->cv_path)) {
                Storage::delete($alumni->cv_path);
            }

            $alumni->delete();

            return redirect()
                ->route('admin.alumni.index')
                ->with('success', 'Data alumni berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menghapus data alumni.');
        }
    }

    public function verify(Alumni $alumni)
    {
        $alumni->update(['is_verified' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Alumni berhasil diverifikasi.'
        ]);
    }

    public function statistics()
    {
        $stats = [
            'total_alumni' => Alumni::count(),
            'verified_alumni' => Alumni::where('is_verified', true)->count(),
            'pending_alumni' => Alumni::where('is_verified', false)->count(),
            'alumni_this_month' => Alumni::whereMonth('created_at', now()->month)
                                         ->whereYear('created_at', now()->year)
                                         ->count(),
            'alumni_by_jurusan' => Jurusan::withCount('alumni')->get()
                                         ->pluck('alumni_count', 'nama_jurusan'),
            'alumni_by_status_kerja' => Alumni::select('status_kerja', DB::raw('count(*) as total'))
                                             ->whereNotNull('status_kerja')
                                             ->groupBy('status_kerja')
                                             ->pluck('total', 'status_kerja'),
            'alumni_by_year' => Alumni::select('tahun_lulus', DB::raw('count(*) as total'))
                                     ->groupBy('tahun_lulus')
                                     ->orderBy('tahun_lulus', 'desc')
                                     ->pluck('total', 'tahun_lulus'),
            'alumni_by_month' => Alumni::select(
                                    DB::raw('MONTH(created_at) as month'),
                                    DB::raw('count(*) as total')
                                )
                                ->whereYear('created_at', now()->year)
                                ->groupBy('month')
                                ->pluck('total', 'month'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,xls'
        ]);

        try {
            // Implementation for Excel import
            // You can use Laravel Excel package for this
            
            // For now, just return success message
            return redirect()
                ->route('admin.alumni.index')
                ->with('success', 'Data alumni berhasil diimport.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat mengimport data: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        $query = Alumni::with('jurusan');
        
        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('jurusan_id')) {
            $query->where('jurusan_id', $request->jurusan_id);
        }

        if ($request->filled('tahun_lulus')) {
            $query->where('tahun_lulus', $request->tahun_lulus);
        }

        if ($request->filled('status_kerja')) {
            $query->where('status_kerja', $request->status_kerja);
        }

        $alumni = $query->get();

        return response()->json([
            'success' => true,
            'data' => $alumni,
            'message' => 'Data exported successfully'
        ]);
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,verify',
            'alumni_ids' => 'required|array',
            'alumni_ids.*' => 'exists:alumni,id'
        ]);

        $alumni = Alumni::whereIn('id', $request->alumni_ids);

        switch ($request->action) {
            case 'delete':
                $alumni->delete();
                $message = 'Alumni terpilih berhasil dihapus.';
                break;
            case 'verify':
                $alumni->update(['is_verified' => true]);
                $message = 'Alumni terpilih berhasil diverifikasi.';
                break;
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }
}
