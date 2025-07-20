<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user in admins table
        Admin::create([
            'nama' => 'Admin BKK SMKN 1 Surabaya',
            'username' => 'admin',
            'password' => 'admin123',
        ]);
    }
}
