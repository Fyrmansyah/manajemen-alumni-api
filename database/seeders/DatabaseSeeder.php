<?php

namespace Database\Seeders;

use App\Models\RangeGaji;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            JalurMasukKuliahSeeder::class,
            MasaTungguKerjaSeeder::class,
            JenisPerusahaanSeeder::class,
            DurasiKerjaSeeder::class,
            RangeGajiSeeder::class,
            KepemilikanUsahaSeeder::class,
            RangeLabaSeeder::class,
            AdminSeeder::class,
            JurusanSeeder::class,
            // NisnSeeder::class,
            // AlumniSeeder::class,
            CompanySeeder::class,
            JobPostingSeeder::class,
            NewsSeeder::class,
        ]);
    }
}
