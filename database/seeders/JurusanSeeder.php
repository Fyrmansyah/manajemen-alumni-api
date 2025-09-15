<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JurusanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Jurusan::create(['nama' => 'Rekayasa Perangkat Lunak', 'tgl_berdiri' => Carbon::create(1996, 8, 19)]);
        Jurusan::create(['nama' => 'Desain Komunikasi Visual', 'tgl_berdiri' => Carbon::create(1997, 7, 20)]);
        Jurusan::create(['nama' => 'Akuntansi', 'tgl_berdiri' => Carbon::create(1998, 1, 2)]);
        Jurusan::create(['nama' => 'Bisnis Digital', 'tgl_berdiri' => Carbon::create(1999, 2, 3)]);
        Jurusan::create(['nama' => 'Manajemen Perkantoran', 'tgl_berdiri' => Carbon::create(2000, 5, 6)]);
        Jurusan::create(['nama' => 'Manajemen Logistik', 'tgl_berdiri' => Carbon::create(2000, 5, 6)]);
        Jurusan::create(['nama' => 'Teknik Komputer dan Jaringan', 'tgl_berdiri' => Carbon::create(2000, 5, 6)]);
        Jurusan::create(['nama' => 'Produksi Siaran Program dan Pertelevisian', 'tgl_berdiri' => Carbon::create(2000, 5, 6)]);
        Jurusan::create(['nama' => 'Perhotelan', 'tgl_berdiri' => Carbon::create(2000, 5, 6)]);
    }
}
