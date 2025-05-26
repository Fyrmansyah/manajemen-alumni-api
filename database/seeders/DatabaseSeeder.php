<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Alumni;
use App\Models\Jurusan;
use App\Models\User;
use Carbon\Carbon;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Admin::firstOrCreate([
            'username' => 'firman',
            'password' => 'firman123',
        ]);

        $rpl = Jurusan::create(['nama' => 'Rekayasa Perangkat Lunak', 'tgl_berdiri' => Carbon::create(1996, 8, 19)]);
        Jurusan::create(['nama' => 'Desain Komunikasi Visual', 'tgl_berdiri' => Carbon::create(1997, 7, 20)]);
        Jurusan::create(['nama' => 'Akuntansi', 'tgl_berdiri' => Carbon::create(1998, 1, 2)]);
        Jurusan::create(['nama' => 'Bisnis Digital', 'tgl_berdiri' => Carbon::create(1999, 2, 3)]);
        Jurusan::create(['nama' => 'Otomatisasi Tata Kelola Perkantoran', 'tgl_berdiri' => Carbon::create(2000, 5, 6)]);

        Alumni::create([
            'nama' => 'David Bontha',
            'tgl_lahir' => '1996-08-19',
            'tahun_mulai' => 2021,
            'tahun_lulus' => 2024,
            'no_tlp' => '08813573779',
            'email' => 'david@gmail.com',
            'alamat' => 'Jl. Rumah Tentram Gembira No. 9',
            'tempat_kerja' => null,
            'jabatan_kerja' => null,
            'tempat_kuliah' => 'Harvard',
            'prodi_kuliah' => 'Teknik Informatika',
            'kesesuaian_kerja' => null,
            'kesesuaian_kuliah' => true,
            'photo' => null,
            'jurusan_id' => $rpl->id
        ]);

        Alumni::factory()->count(50)->create();
    }
}
