<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AlumniDashboardController extends Controller
{
    public function index()
    {
        $alumni = Auth::guard('alumni')->user(); // Use alumni guard
        
        if (!$alumni) {
            return redirect()->route('login');
        }
        
        // Get statistics
        $stats = [
            'total_applications' => Application::where('alumni_id', $alumni->id)->count(),
            'pending_applications' => Application::where('alumni_id', $alumni->id)->where('status', 'submitted')->count(),
            'accepted_applications' => Application::where('alumni_id', $alumni->id)->where('status', 'accepted')->count(),
            'available_jobs' => Job::where('status', 'active')->count(),
        ];

        // Get recent applications
        $recentApplications = Application::with(['job.company'])
            ->where('alumni_id', $alumni->id)
            ->latest()
            ->take(5)
            ->get();

        // Get recommended jobs (based on graduation year, major, etc.)
        $recommendedJobs = Job::with('company')
            ->where('status', 'active')
            ->latest()
            ->take(6)
            ->get();

        return view('alumni.dashboard', compact('stats', 'recentApplications', 'recommendedJobs'));
    }

    public function applications(Request $request)
    {
        $alumni = Auth::guard('alumni')->user();
        
        if (!$alumni) {
            return redirect()->route('login');
        }
        
        $query = Application::with(['job.company'])
            ->where('alumni_id', $alumni->id);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $applications = $query->latest()->paginate(10);

        return view('alumni.applications', compact('applications'));
    }

    public function profile()
    {
        $alumni = Auth::guard('alumni')->user();
        
        if (!$alumni) {
            return redirect()->route('login');
        }
        
        // Get jurusan data for dropdown
        $jurusans = \App\Models\Jurusan::orderBy('nama')->get();
        
        return view('alumni.profile', compact('alumni', 'jurusans'));
    }

    public function updateProfile(Request $request)
    {
        $alumni = Auth::guard('alumni')->user();
        
        if (!$alumni) {
            return redirect()->route('login');
        }
        
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:alumnis,email,' . $alumni->id,
            'phone' => 'required|string|max:20',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            // NISN harus ada di tabel master (exists) bukan membuat baru
            'nisn' => 'required|digits:10|exists:nisns,number',
            'jurusan_id' => 'required|exists:jurusans,id',
            'tahun_lulus' => 'required|integer|min:2015|max:' . (date('Y') + 1),
            'alamat' => 'required|string|max:500',
            'pengalaman_kerja' => 'nullable|string|max:1000',
            'keahlian' => 'nullable|string|max:1000',
            'whatsapp_notifications' => 'nullable|boolean',
            'tempat_kuliah' => 'nullable|string|max:255',
            'prodi_kuliah' => 'nullable|string|max:255',
            'tempat_kerja' => 'nullable|string|max:255',
            'jabatan_kerja' => 'nullable|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only([
            'nama_lengkap', 'email', 'phone', 'tanggal_lahir', 'jenis_kelamin',
            'jurusan_id', 'tahun_lulus', 'alamat', 'pengalaman_kerja', 'keahlian',
            'tempat_kuliah', 'prodi_kuliah', 'tempat_kerja', 'jabatan_kerja'
        ]);

        // Map NISN -> nisn_id tanpa membuat data baru; pastikan tidak dipakai alumni lain
        if ($request->filled('nisn')) {
            $nisnNumber = $request->input('nisn');
            $nisnModel = \App\Models\Nisn::where('number', $nisnNumber)->first();
            if (!$nisnModel) {
                return back()->withErrors(['nisn' => 'NISN tidak ditemukan dalam data master.'])->withInput();
            }
            $isUsed = \App\Models\Alumni::where('nisn_id', $nisnModel->id)->where('id', '!=', $alumni->id)->exists();
            if ($isUsed) {
                return back()->withErrors(['nisn' => 'NISN sudah digunakan oleh alumni lain.'])->withInput();
            }
            $data['nisn_id'] = $nisnModel->id;
        }

        // Handle WhatsApp notifications checkbox
        $data['whatsapp_notifications'] = $request->has('whatsapp_notifications');

        // Handle photo upload (replace & delete old)
        if ($request->hasFile('foto')) {
            try {
                $old = $alumni->foto;
                $path = $request->file('foto')->store('alumni_photos', 'public');
                $data['foto'] = basename($path);
                if ($old && $old !== $data['foto']) {
                    $oldRelative = str_starts_with($old, 'alumni_photos/') ? $old : 'alumni_photos/' . ltrim($old,'/');
                    if (Storage::disk('public')->exists($oldRelative)) {
                        Storage::disk('public')->delete($oldRelative);
                    }
                }
            } catch (\Throwable $e) {
                return back()->with('error', 'Gagal mengunggah foto: ' . $e->getMessage());
            }
        }

        // Update both new and old field names for compatibility
        $alumni->update($data);

        // Sinkronisasi field lama untuk kompatibilitas
        $alumni->update([
            'nama' => $data['nama_lengkap'],
            'no_tlp' => $data['phone'],
            'tgl_lahir' => $data['tanggal_lahir'],
        ]);

    // Refresh instance (dan guard user) agar foto & timestamp terbaru langsung muncul setelah redirect
    $alumni->refresh();
    Auth::guard('alumni')->setUser($alumni);

    return back()->with('success', 'Profil berhasil diperbarui!');
    }
}
