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


        $data = [];

        for ($i = 1; $i <= 100; $i++) {
            $data[] = ['nama' => "jalur masuk kuliah - {$i}"];
        }

        JalurMasukKuliah::insert($data);
    }
}
