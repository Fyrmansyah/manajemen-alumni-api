<?php

namespace App\Exports;

use App\Models\Alumni;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AlumnisExport implements FromQuery, WithHeadings, WithMapping

{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Alumni::query()
            ->with(['nisn', 'jurusan', 'latestKuliah', 'latestKerja', 'latestUsaha']);

        if (empty($this->filters)) {
            return $query;
        }

        $kerja   = $this->filters['kerja']   ?? false;
        $kuliah  = $this->filters['kuliah']  ?? false;
        $usaha   = $this->filters['usaha']   ?? false;
        $jobless = $this->filters['jobless'] ?? false;

        // Filter non-jobless dengan OR
        if ($kerja || $kuliah || $usaha) {
            $query->where(function ($q) use ($kerja, $kuliah, $usaha) {

                if ($kerja) {
                    $q->orWhereHas('kerjas');
                }

                if ($kuliah) {
                    $q->orWhereHas('kuliahs');
                }

                if ($usaha) {
                    $q->orWhereHas('usahas');
                }
            });
        }

        // Filter jobless
        if ($jobless) {
            $query->orWhere(function ($q) {
                $q->whereDoesntHave('kerjas')
                    ->whereDoesntHave('kuliahs')
                    ->whereDoesntHave('usahas');
            });
        }


        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama',
            'Jenis_Kelamin',
            'Tempat_Lahir',
            'Tanggal_Lahir',
            'Email',
            'No_Tlp',
            'Tahun_Masuk_Sekolah',
            'Tahun_Lulus_Sekolah',
            'Alamat_Jalan',
            'Alamat_RT',
            'Alamat_RW',
            'Alamat_Desa',
            'Alamat_Kelurahan',
            'Alamat_Kecamatan',
            'Alamat_Kode_Pos',
            'Jurusan',
            'NISN',
            'Tempat_Kuliah',
            'tahun_Masuk_Kuliah',
            'tahun_Lulus_Kuliah',
            'Prodi_Kuliah',
            'Kesesuaian_Kuliah',
            'Tempat_Kerja',
            'Tgl_Masuk_Kerja',
            'Tgl_Selesai_Kerja',
            'Kesesuaian_Kerja',
            'Nama_Usaha',
            'Bidang_Usaha',
            'Tgl_Mulai_Usaha',
            'Tgl_Selesai_Usaha',
            'Kesesuaian_Usaha',
        ];
    }

    public function map($alumni): array
    {
        return [
            $alumni->id,
            $alumni->nama ?? '-',
            $alumni->jenis_kelamin ?? '-',
            $alumni->tempat_lahir ?? '-',
            $alumni->tgl_lahir ?? '-',
            $alumni->email ?? '-',
            $alumni->no_tlp ?? '-',
            $alumni->tahun_mulai ?? '-',
            $alumni->tahun_lulus ?? '-',
            $alumni->alamat ?? '-',
            $alumni->alamat_rt ?? '-',
            $alumni->alamat_rw ?? '-',
            $alumni->alamat_desa ?? '-',
            $alumni->alamat_kelurahan ?? '-',
            $alumni->alamat_kecamatan ?? '-',
            $alumni->alamat_kode_pos ?? '-',
            $alumni->jurusan->nama ?? '-',
            $alumni->nisn->number ?? '-',
            $alumni->latestKuliah?->nama ?? '-',
            $alumni->latestKuliah?->tahun_masuk ?? '-',
            $alumni->latestKuliah?->tahun_lulus ?? '-',
            $alumni->latestKuliah?->prodi ?? '-',
            $alumni->latestKuliah?->sesuai_jurusan ? 'Y' : 'N',
            $alumni->latestKerja?->nama_perusahaan ?? '-',
            $alumni->latestKerja?->tgl_mulai ?? '-',
            $alumni->latestKerja?->tgl_selesai ?? '-',
            $alumni->latestKerja?->sesuai_jurusan ? 'Y' : 'N',
            $alumni->latestUsaha?->nama_perusahaan ?? '-',
            $alumni->latestUsaha?->bidang_usaha ?? '-',
            $alumni->latestUsaha?->tgl_mulai ?? '-',
            $alumni->latestUsaha?->tgl_selesai ?? '-',
            $alumni->latestUsaha?->sesuai_jurusan ? 'Y' : 'N',
        ];
    }
}
