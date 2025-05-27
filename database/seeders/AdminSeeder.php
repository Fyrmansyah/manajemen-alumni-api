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
        Admin::create([
            'username' => 'firman',
            'nama' => 'Firman firmansyah',
            'password' => 'firman123',
        ]);
        Admin::create([
            'username' => 'budi123',
            'nama' => 'Budi Budiansyah',
            'password' => 'pw456',
        ]);
        Admin::create([
            'username' => 'eko111',
            'nama' => 'Eko Patrio',
            'password' => 'eko123',
        ]);
    }
}
