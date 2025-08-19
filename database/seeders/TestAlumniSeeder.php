<?php

namespace Database\Seeders;

use App\Models\Alumni;
use App\Models\Jurusan;
use Illuminate\Database\Seeder;

class TestAlumniSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First create some jurusan if they don't exist
        $jurusans = [
            ['nama' => 'Teknik Komputer dan Jaringan'],
            ['nama' => 'Rekayasa Perangkat Lunak'],
            ['nama' => 'Multimedia'],
            ['nama' => 'Sistem Informasi'],
        ];

        foreach ($jurusans as $jurusan) {
            Jurusan::firstOrCreate($jurusan, $jurusan);
        }

        // Create test alumni data with only existing columns
        $alumni = [
            [
                'nama_lengkap' => 'Julian Fernando Pratama',
                'nama' => 'Julian Fernando',
                'nisn' => '1234567890',
                'email' => 'julian.fernando@gmail.com',
                'password' => bcrypt('password123'),
                'no_tlp' => '081234567890',
                'alamat' => 'Jl. Merdeka No. 123, Surabaya',
                'tgl_lahir' => '2000-05-15',
                'jenis_kelamin' => 'L',
                'jurusan_id' => 1,
                'tahun_mulai' => 2015,
                'tahun_lulus' => 2018,
                'tempat_kerja' => 'PT. Tech Indonesia',
                'jabatan_kerja' => 'Software Developer',
                'is_verified' => false,
            ],
            [
                'nama_lengkap' => 'Siti Nurhaliza Putri',
                'nama' => 'Siti Nurhaliza',
                'nisn' => '9876543210',
                'email' => 'siti.nurhaliza@gmail.com',
                'password' => bcrypt('password123'),
                'no_tlp' => '082345678901',
                'alamat' => 'Jl. Pahlawan No. 456, Surabaya',
                'tgl_lahir' => '2001-03-20',
                'jenis_kelamin' => 'P',
                'jurusan_id' => 2,
                'tahun_mulai' => 2016,
                'tahun_lulus' => 2019,
                'tempat_kuliah' => 'Universitas Negeri Surabaya',
                'prodi_kuliah' => 'Teknik Informatika',
                'is_verified' => true,
            ],
            [
                'nama_lengkap' => 'Ahmad Rizki Pratama',
                'nama' => 'Ahmad Rizki',
                'nisn' => '1357924680',
                'email' => 'ahmad.rizki@gmail.com',
                'password' => bcrypt('password123'),
                'no_tlp' => '083456789012',
                'alamat' => 'Jl. Pemuda No. 789, Surabaya',
                'tgl_lahir' => '1999-12-10',
                'jenis_kelamin' => 'L',
                'jurusan_id' => 3,
                'tahun_mulai' => 2014,
                'tahun_lulus' => 2017,
                'tempat_kerja' => 'CV. Kreatif Media',
                'jabatan_kerja' => 'Graphic Designer',
                'is_verified' => true,
            ]
        ];

        foreach ($alumni as $data) {
            Alumni::firstOrCreate(
                ['nisn' => $data['nisn']],
                $data
            );
        }
    }
}
