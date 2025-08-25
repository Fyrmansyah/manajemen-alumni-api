<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NisnImportRequest;
use App\Models\Nisn;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class NisnImportController extends Controller
{
    public function form()
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('login');
        }
        return view('admin.nisn.import');
    }

    public function upload(NisnImportRequest $request)
    {
        $file = $request->file('file');
        if (!$file->isValid()) {
            return back()->with('error', 'Upload gagal: file tidak valid.');
        }

        // Gunakan path sementara langsung tanpa memindahkan file
        $tempPath = $file->getRealPath();
        $handle = @fopen($tempPath, 'r');

        // Jika gagal (beberapa environment), fallback simpan manual lalu buka
        if (!$handle) {
            if (!is_dir(storage_path('app/temp'))) {
                @mkdir(storage_path('app/temp'), 0775, true);
            }
            $storedPath = $file->storeAs('temp', uniqid('nisn_') . '.csv');
            $full = storage_path('app/' . $storedPath);
            if (!file_exists($full)) {
                return back()->with('error', 'Gagal akses file upload (cek permission storage/app).');
            }
            $handle = @fopen($full, 'r');
            if (!$handle) {
                return back()->with('error', 'Tidak bisa membuka file yang diupload (fopen gagal).');
            }
            $cleanup = $full;
        } else {
            $cleanup = null; // tempPath dikelola PHP
        }
        $batch = []; $now = now();
        $inserted = 0; $skippedFmt = 0; $existing = 0; $total = 0;
        while ($handle && ($line = fgetcsv($handle)) !== false) {
            $total++;
            if (!isset($line[0])) { $skippedFmt++; continue; }
            $raw = trim($line[0]);
            if ($raw === '' || !preg_match('/^\d{5,}$/', $raw)) { $skippedFmt++; continue; }
            if (Nisn::where('number', $raw)->exists()) { $existing++; continue; }
            $batch[] = [ 'number' => $raw, 'created_at' => $now, 'updated_at' => $now ];
            if (count($batch) === 1000) { Nisn::insert($batch); $inserted += count($batch); $batch = []; }
        }
        if (is_resource($handle)) {
            fclose($handle);
        }
        if ($batch) { Nisn::insert($batch); $inserted += count($batch); }
        // (handle already closed)
        if ($cleanup && file_exists($cleanup)) {
            @unlink($cleanup);
        }
        return back()->with('success', "Import selesai. Total=$total, inserted=$inserted, duplikat=$existing, format_salah=$skippedFmt");
    }
}
