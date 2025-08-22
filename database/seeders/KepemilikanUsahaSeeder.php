<?php

namespace Database\Seeders;

use App\Models\KepemilikanUsaha;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KepemilikanUsahaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        KepemilikanUsaha::create(['value' => 'Milik Sendiri']);
        KepemilikanUsaha::create(['value' => 'Milik Bersama']);
    }
}
