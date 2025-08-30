<?php

namespace Database\Seeders;

use App\Models\Alumni;
use App\Models\Jurusan;
use App\Models\Nisn;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AlumniSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jurusan = Jurusan::first();
        $nisn = Nisn::first();

        Alumni::create([
            'nama' => 'Budi Pekerti',
            'jenis_kelamin' => 'L',
            'tgl_lahir' => '1996-08-19',
            'tahun_mulai' => 2021,
            'tahun_lulus' => 2024,
            'no_tlp' => '08813573779',
            'email' => 'budi@gmail.com',
            'password' => bcrypt('test123'),
            'jurusan_id' => $jurusan->id,
            'nisn_id' => $nisn?->id,
            'alamat_jalan' => 'jalan test',
            'alamat_rt' => 1,
            'alamat_rw' => 1,
            'alamat_desa' => 'desa test',
            'alamat_kelurahan' => 'kelurahan test',
            'alamat_kecamatan' => 'kecamatan test',
            'alamat_kode_pos' => 'kode_pos test',
            'tempat_lahir' => 'tempat_lahir test',
        ]);

        // Alumni::factory()->count(50)->create();
    }
}
