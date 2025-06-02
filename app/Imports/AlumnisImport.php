<?php

namespace App\Imports;

use App\Models\Alumni;
use App\Models\Jurusan;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AlumnisImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $jurusan = Jurusan::firstOrCreate(
            ['nama' => $row['nama_jurusan']],
            ['tgl_berdiri' => Carbon::now()->format('Y-m-d')]
        );

        return new Alumni([
            'nama' => $row['nama'],
            'tgl_lahir' => $this->excelDateToCarbon($row['tgl_lahir']),
            'tahun_mulai' => $row['tahun_mulai'],
            'tahun_lulus' => $row['tahun_lulus'],
            'no_tlp' => $row['no_tlp'],
            'email' => $row['email'],
            'password' => isset($row['password']) ? $row['password'] : null,
            'alamat' => $row['alamat'],
            'tempat_kerja' => $row['tempat_kerja'] ?? null,
            'jabatan_kerja' => $row['jabatan_kerja'] ?? null,
            'tempat_kuliah' => $row['tempat_kuliah'] ?? null,
            'prodi_kuliah' => $row['prodi_kuliah'] ?? null,
            'kesesuaian_kerja' => isset($row['kesesuaian_kerja']) ? filter_var($row['kesesuaian_kerja'], FILTER_VALIDATE_BOOLEAN) : null,
            'kesesuaian_kuliah' => isset($row['kesesuaian_kuliah']) ? filter_var($row['kesesuaian_kuliah'], FILTER_VALIDATE_BOOLEAN) : null,
            'jurusan_id' => $jurusan->id,
        ]);
    }

    function excelDateToCarbon($excelDate)
    {
        if (is_numeric($excelDate)) {
            // Konversi serial Excel ke Unix timestamp
            $timestamp = ($excelDate - 25569) * 86400;
            // Buat objek Carbon dari timestamp (UTC)
            return Carbon::createFromTimestampUTC($timestamp)->format('Y-m-d');
        }

        // Jika bukan angka, coba parsing langsung sebagai tanggal string
        return Carbon::parse($excelDate);
    }
}
