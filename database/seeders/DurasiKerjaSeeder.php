<?php

namespace Database\Seeders;

use App\Models\DurasiKerja;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DurasiKerjaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DurasiKerja::create(['value' => '7 - 12']);
        DurasiKerja::create(['value' => '12 - 6']);
    }
}
