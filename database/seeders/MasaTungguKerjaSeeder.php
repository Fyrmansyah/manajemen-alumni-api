<?php

namespace Database\Seeders;

use App\Models\MasaTungguKerja;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasaTungguKerjaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MasaTungguKerja::create(['value' => '< 1 minggu']);
        MasaTungguKerja::create(['value' => '1 - 2 minggu']);
        MasaTungguKerja::create(['value' => '> 4 minggu']);
    }
}
