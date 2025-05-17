<?php

namespace Database\Seeders;

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

        User::firstOrCreate([
            'name' => 'Firman',
            'username' => 'firman',
            'password' => 'firman123',
        ]);

        Jurusan::create(['nama' => 'Rekayasa Perangkat Lunak', 'tgl_berdiri' => Carbon::create(1996, 8, 19)]);
        Jurusan::create(['nama' => 'Desain Komunikasi Visual', 'tgl_berdiri' => Carbon::create(1997, 7, 20)]);
        Jurusan::create(['nama' => 'Akuntansi', 'tgl_berdiri' => Carbon::create(1998, 1, 2)]);
        Jurusan::create(['nama' => 'Bisnis Digital', 'tgl_berdiri' => Carbon::create(1999, 2, 3)]);
        Jurusan::create(['nama' => 'Otomatisasi Tata Kelola Perkantoran', 'tgl_berdiri' => Carbon::create(2000, 5, 6)]);
        Alumni::factory()->count(50)->create();
    }
}
