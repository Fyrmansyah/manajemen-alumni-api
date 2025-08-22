<?php

namespace Database\Seeders;

use App\Models\RangeLaba;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RangeLabaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RangeLaba::create(['value' => '< 5 juta']);
        RangeLaba::create(['value' => '< 10 juta']);
        RangeLaba::create(['value' => '> 10 juta']);
    }
}
