<?php

namespace Database\Seeders;

use App\Models\Nisn;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NisnSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Nisn::create(["number" => "123"]);
        Nisn::create(["number" => "345"]);
        Nisn::create(["number" => "678"]);
        Nisn::create(["number" => "91011"]);
    }
}
