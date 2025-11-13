<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AlumnisExport;

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
                                    ->orWhereHas('nisnNumber', function($qq) use ($search) { $qq->where('number','like', "%{$search}%"); })
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
            // Determine latest/default CV for preview/download
            $cv = $alumni->cvs()
                ->when(true, function ($q) { $q->orderByDesc('is_default')->latest(); })
                ->first();

            // Build photo URL using public storage if available
            $photoUrl = null;
            if (!empty($alumni->foto)) {
                // If only filename stored, prefix with alumni_photos/
                $relative = (strpos($alumni->foto, 'alumni_photos/') === 0)
                    ? $alumni->foto
                    : ('alumni_photos/' . ltrim($alumni->foto, '/'));
                $photoUrl = asset('storage/' . $relative);
            }

            // Normalisasi data agar front-end tidak mendapatkan null untuk field alternatif
            $normalized = [
                'id' => $alumni->id,
                'nama' => $alumni->nama,
                'nama_lengkap' => $alumni->nama_lengkap ?? $alumni->nama,
                'nisn' => $alumni->nisn,
                'email' => $alumni->email,
                'phone' => $alumni->phone ?? $alumni->no_hp ?? $alumni->no_tlp,
                'no_hp' => $alumni->no_hp ?? $alumni->no_tlp,
                'no_tlp' => $alumni->no_tlp,
                'alamat' => $alumni->alamat,
                'tanggal_lahir' => $alumni->tanggal_lahir ?? $alumni->tgl_lahir,
                'tgl_lahir' => $alumni->tgl_lahir,
                'jenis_kelamin' => $alumni->jenis_kelamin,
                'tahun_lulus' => $alumni->tahun_lulus,
                'jurusan' => $alumni->jurusan ? [
                    'id' => $alumni->jurusan->id,
                    'nama' => $alumni->jurusan->nama,
                ] : null,
                'status_kerja' => $alumni->status_kerja,
                'perusahaan' => $alumni->perusahaan,
                'posisi' => $alumni->posisi,
                'gaji' => $alumni->gaji,
                'is_verified' => (bool) $alumni->is_verified,
                'whatsapp_notifications' => (bool) $alumni->whatsapp_notifications,
                // Backward compat fields
                'cv_path' => $cv ? $cv->filename : null,
                'foto' => $alumni->foto,
                // New helper fields for UI
                'photo_url' => $photoUrl,
                'cv_exists' => (bool) $cv,
                'cv_url' => $cv ? asset('storage/cvs/' . $cv->filename) : null,
                'created_at' => $alumni->created_at,
                // Field pekerjaan/kuliah tambahan jika ada di tabel
                'tempat_kerja' => $alumni->tempat_kerja ?? $alumni->perusahaan,
                'jabatan_kerja' => $alumni->jabatan_kerja ?? $alumni->posisi,
                'kesesuaian_kerja' => $alumni->kesesuaian_kerja,
                'tempat_kuliah' => $alumni->tempat_kuliah,
                'prodi_kuliah' => $alumni->prodi_kuliah,
                'kesesuaian_kuliah' => $alumni->kesesuaian_kuliah,
                'pengalaman_kerja' => $alumni->pengalaman_kerja,
                'keahlian' => $alumni->keahlian,
            ];

            return response()->json([
                'success' => true,
                'data' => $normalized,
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
        // Validasi menyesuaikan kolom wajib pada tabel alumnis (legacy + baru)
        $request->validate([
            'nama' => 'required|string|max:255',
            'nama_lengkap' => 'nullable|string|max:255',
            // NISN harus sudah ada di master nisns (import dapodik) dan belum dipakai alumni lain
            'nisn' => 'required|digits:10|exists:nisns,number',
            'email' => 'required|email|unique:alumnis,email',
            'no_tlp' => 'required|string|max:20',
            'alamat' => 'required|string',
            'jurusan_id' => 'required|exists:jurusans,id',
            'tahun_mulai' => 'required|integer|min:2000|max:' . (date('Y') + 5),
            'tahun_lulus' => 'required|integer|min:2000|max:' . (date('Y') + 5),
            'jenis_kelamin' => 'required|in:L,P',
            'tgl_lahir' => 'required|date',
            'password' => 'required|string|min:6',
            // Opsional
            'tempat_kerja' => 'nullable|string|max:255',
            'jabatan_kerja' => 'nullable|string|max:255',
            'tempat_kuliah' => 'nullable|string|max:255',
            'prodi_kuliah' => 'nullable|string|max:255',
            'kesesuaian_kerja' => 'nullable|in:0,1',
            'kesesuaian_kuliah' => 'nullable|in:0,1',
            'pengalaman_kerja' => 'nullable|string',
            'keahlian' => 'nullable|string',
            'is_verified' => 'nullable|boolean',
        ]);

        // Susun data sesuai kolom yang ada di DB, sekaligus mirroring ke field baru
        // Ambil record NISN master
        $nisnModel = \App\Models\Nisn::where('number', $request->input('nisn'))->first();
        if (!$nisnModel) {
            return back()->withErrors(['nisn' => 'NISN tidak ditemukan pada data master.'])->withInput();
        }
        if (\App\Models\Alumni::where('nisn_id', $nisnModel->id)->exists()) {
            return back()->withErrors(['nisn' => 'NISN sudah terpakai oleh alumni lain.'])->withInput();
        }

        $data = [
            'nama' => $request->input('nama') ?: $request->input('nama_lengkap'),
            'nama_lengkap' => $request->input('nama_lengkap') ?: $request->input('nama'),
            // Map ke record master yang sudah ada
            'nisn_id' => $nisnModel->id,
            'email' => $request->input('email'),
            'password' => $request->input('password'), // otomatis di-hash via cast di model
            'no_tlp' => $request->input('no_tlp'),
            'phone' => $request->input('no_tlp'), // mirror ke field baru
            'alamat' => $request->input('alamat'),
            'jurusan_id' => $request->input('jurusan_id'),
            'tahun_mulai' => (int) $request->input('tahun_mulai'),
            'tahun_lulus' => (int) $request->input('tahun_lulus'),
            'jenis_kelamin' => $request->input('jenis_kelamin'),
            'tgl_lahir' => $request->input('tgl_lahir'),
            'tanggal_lahir' => $request->input('tgl_lahir'), // mirror ke field baru
            'tempat_kerja' => $request->input('tempat_kerja'),
            'jabatan_kerja' => $request->input('jabatan_kerja'),
            'tempat_kuliah' => $request->input('tempat_kuliah'),
            'prodi_kuliah' => $request->input('prodi_kuliah'),
            'kesesuaian_kerja' => $request->input('kesesuaian_kerja'),
            'kesesuaian_kuliah' => $request->input('kesesuaian_kuliah'),
            'pengalaman_kerja' => $request->input('pengalaman_kerja'),
            'keahlian' => $request->input('keahlian'),
            'is_verified' => $request->boolean('is_verified'),
        ];

        Alumni::create($data);

        return redirect()->route('admin.alumni.index')
            ->with('success', 'Data alumni berhasil ditambahkan.');
    }

    public function edit(Alumni $alumni)
    {
        $jurusans = Jurusan::all();
        return view('admin.alumni.edit', compact('alumni', 'jurusans'));
    }

    public function update(Request $request, Alumni $alumni)
    {
        // Validasi: gunakan field yang tersedia pada form edit + mapping ke kolom DB
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            // Hanya boleh memilih NISN yang ada di master dan belum dipakai alumni lain (kecuali dirinya)
            'nisn' => 'required|digits:10|exists:nisns,number',
            'email' => 'required|email|unique:alumnis,email,' . $alumni->id,
            'no_hp' => 'nullable|string|max:20', // akan dipetakan ke no_tlp
            'alamat' => 'nullable|string',
            'jurusan_id' => 'required|exists:jurusans,id',
            'tahun_lulus' => 'required|integer|min:2000|max:' . (date('Y') + 5),
            // Field opsional pada form edit
            'perusahaan' => 'nullable|string|max:255', // dipetakan ke tempat_kerja
            'posisi' => 'nullable|string|max:255',     // dipetakan ke jabatan_kerja
            'gaji' => 'nullable|numeric|min:0',        // tidak ada kolom di DB, diabaikan
            'tempat_kuliah' => 'nullable|string|max:255',
            'prodi_kuliah' => 'nullable|string|max:255',
            'status_kerja' => 'nullable|in:bekerja,kuliah,wirausaha,menganggur,belum_diisi',
            'is_verified' => 'nullable|boolean',
        ]);

        $nisnModel = \App\Models\Nisn::where('number', $request->input('nisn'))->first();
        if (!$nisnModel) {
            return back()->withErrors(['nisn' => 'NISN tidak ditemukan pada data master.'])->withInput();
        }
        if ($nisnModel->id !== $alumni->nisn_id && \App\Models\Alumni::where('nisn_id', $nisnModel->id)->exists()) {
            return back()->withErrors(['nisn' => 'NISN sudah terpakai oleh alumni lain.'])->withInput();
        }

        $data = [
            'nama_lengkap' => $request->input('nama_lengkap'),
            // Jangan ubah 'nama' di edit jika tidak ada field; tetap gunakan existing jika kosong
            'nama' => $alumni->nama ?: $request->input('nama_lengkap'),
            'nisn_id' => $nisnModel->id,
            'email' => $request->input('email'),
            'no_tlp' => $request->filled('no_hp') ? $request->input('no_hp') : $alumni->no_tlp,
            'phone' => $request->filled('no_hp') ? $request->input('no_hp') : $alumni->phone,
            'alamat' => $request->input('alamat'),
            'jurusan_id' => $request->input('jurusan_id'),
            'tahun_lulus' => (int) $request->input('tahun_lulus'),
            // Pemetaan perusahaan/posisi ke kolom yang ada
            'tempat_kerja' => $request->input('tempat_kerja') ?: $request->input('perusahaan'),
            'jabatan_kerja' => $request->input('jabatan_kerja') ?: $request->input('posisi'),
            'tempat_kuliah' => $request->input('tempat_kuliah'),
            'prodi_kuliah' => $request->input('prodi_kuliah'),
            'status_kerja' => $request->input('status_kerja') ?: 'belum_diisi',
            'is_verified' => $request->boolean('is_verified'),
        ];

        // Hapus key yang nilainya null agar tidak memaksa null pada kolom not-null yang tidak diedit
        $data = array_filter($data, function ($v) {
            return !is_null($v);
        });

        $alumni->update($data);

        return redirect()->route('admin.alumni.index')
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
                                         ->pluck('alumni_count', 'nama'),
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
                                    ->orWhereHas('nisnNumber', function($qq) use ($search){ $qq->where('number','like', "%{$search}%"); })
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

    $filename = 'alumni-' . now()->format('Ymd-His') . '.xlsx';
    return Excel::download(new AlumnisExport($alumni), $filename);
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,verify',
            'alumni_ids' => 'required|array',
            'alumni_ids.*' => 'exists:alumnis,id'
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
