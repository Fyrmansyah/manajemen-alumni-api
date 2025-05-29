<?php

namespace Database\Seeders;

use App\Models\Alumni;
use App\Models\Jurusan;
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
        Alumni::create([
            'nama' => 'David Bontha',
            'tgl_lahir' => '1996-08-19',
            'tahun_mulai' => 2021,
            'tahun_lulus' => 2024,
            'no_tlp' => '08813573779',
            'email' => 'david@gmail.com',
            'password' => 'test123',
            'alamat' => 'Jl. Rumah Tentram Gembira No. 9',
            'tempat_kerja' => null,
            'jabatan_kerja' => null,
            'tempat_kuliah' => 'Harvard',
            'prodi_kuliah' => 'Teknik Informatika',
            'kesesuaian_kerja' => null,
            'kesesuaian_kuliah' => true,
            'photo' => null,
            'jurusan_id' => $jurusan->id
        ]);

        Alumni::factory()->count(50)->create();
    }
}
