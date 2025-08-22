<?php

namespace Database\Seeders;

use App\Models\RangeGaji;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RangeGajiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RangeGaji::create(['value' => '< 1jt']);
        RangeGaji::create(['value' => '< UMR']);
        RangeGaji::create(['value' => '> UMR']);
    }
}
