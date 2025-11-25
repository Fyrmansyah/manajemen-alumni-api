<?php

namespace Database\Seeders;

use App\Models\JalurMasukKuliah;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JalurMasukKuliahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        JalurMasukKuliah::create(['nama' => 'SNBP']);
        JalurMasukKuliah::create(['nama' => 'SNBT']);
        JalurMasukKuliah::create(['nama' => 'Mandiri - Prestasi']);
    }
}
