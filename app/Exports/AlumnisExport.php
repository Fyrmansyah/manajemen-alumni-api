<?php

namespace App\Exports;

use App\Models\Alumni;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AlumnisExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    use Exportable;

    /** @var \Illuminate\Support\Collection<int, Alumni> */
    protected Collection $alumni;

    /**
     * @param Collection<int, Alumni> $alumni
     */
    public function __construct(Collection $alumni)
    {
        // Ensure jurusan relation loaded to avoid N+1
        $this->alumni = $alumni->loadMissing('jurusan');
    }

    public function collection()
    {
        return $this->alumni;
    }

    public function headings(): array
    {
        return [
            'ID',
            'NISN',
            'Nama',
            'Email',
            'Telepon',
            'Jenis Kelamin',
            'Tanggal Lahir',
            'Jurusan',
            'Tahun Lulus',
            'Status Kerja',
            'Tempat Kerja',
            'Jabatan Kerja',
            'Tempat Kuliah',
            'Prodi Kuliah',
            'Alamat',
            'Terverifikasi',
            'Dibuat',
        ];
    }

    /**
     * @param Alumni $alumni
     */
    public function map($alumni): array
    {
        $phone = $alumni->phone ?: ($alumni->no_hp ?: $alumni->no_tlp);
        $nama = $alumni->nama_lengkap ?: $alumni->nama;
        $jenisKelamin = $alumni->jenis_kelamin === 'L' ? 'Laki-laki' : ($alumni->jenis_kelamin === 'P' ? 'Perempuan' : '');
        $tglLahir = $alumni->tanggal_lahir ?: $alumni->tgl_lahir;

        return [
            $alumni->id,
            $alumni->nisn,
            $nama,
            $alumni->email,
            $phone,
            $jenisKelamin,
            $tglLahir,
            optional($alumni->jurusan)->nama,
            $alumni->tahun_lulus,
            $alumni->status_kerja,
            $alumni->tempat_kerja ?: $alumni->perusahaan,
            $alumni->jabatan_kerja ?: $alumni->posisi,
            $alumni->tempat_kuliah,
            $alumni->prodi_kuliah,
            $alumni->alamat,
            $alumni->is_verified ? 'Ya' : 'Tidak',
            optional($alumni->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
