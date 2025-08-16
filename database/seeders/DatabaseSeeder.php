<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Alumni;
use App\Models\Jurusan;
use App\Models\User;
use Carbon\Carbon;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
