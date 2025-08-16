<?php

namespace Database\Seeders;

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
            AdminSeeder::class,
            JurusanSeeder::class,
            NisnSeeder::class,
            AlumniSeeder::class,
            CompanySeeder::class,
            JobPostingSeeder::class,
            NewsSeeder::class,
        ]);
    }
}
