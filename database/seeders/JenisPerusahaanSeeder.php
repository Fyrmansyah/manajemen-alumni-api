<?php

namespace Database\Seeders;

use App\Models\JenisPerusahaan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JenisPerusahaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        JenisPerusahaan::create(['value' => 'Instansi pemerintah']);
        JenisPerusahaan::create(['value' => 'Lembaga internasional']);
        JenisPerusahaan::create(['value' => 'Lembaga Non-profit']);
        JenisPerusahaan::create(['value' => 'Usaha perorangan']);
    }
}
