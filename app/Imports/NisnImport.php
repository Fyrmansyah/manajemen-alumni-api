<?php

namespace App\Imports;

use App\Models\Nisn;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class NisnImport implements ToModel, WithHeadingRow, WithUpserts
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Nisn([
            'number' => $row['nisn'],
            'nama' => $row['nama'],
            'nik' => $row['nik'],
            'tgl_lahir' => $row['tanggal_lahir'],
            'tempat_lahir' => $row['tempat_lahir'],
            'alamat' => $row['alamat'],
            'rt' => $row['rt'],
            'rw' => $row['rw'],
            'kelurahan' => $row['kelurahan'],
            'kecamatan' => $row['kecamatan'],
            'kode_pos' => $row['kode_pos'],
            'no_tlp' => $row['no_telepon'],
        ]);
    }

    public function uniqueBy()
    {
        return 'number';
    }
}
