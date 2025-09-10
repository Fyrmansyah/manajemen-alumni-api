<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Jurusan;
use Illuminate\Support\Facades\Hash;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $jurusans = Jurusan::all();
        
        $companies = [
            [
                'company_name' => 'PT Teknologi Indonesia',
                'email' => 'hr@teknologiindonesia.com',
                'phone' => '021-5555-0001',
                'address' => 'Jl. Sudirman No. 123, Jakarta Pusat',
                'website' => 'https://teknologiindonesia.com',
                'description' => 'Perusahaan teknologi terkemuka yang fokus pada pengembangan software dan aplikasi mobile.',
                'category_id' => $jurusans->where('nama', 'Teknik Komputer dan Jaringan')->first()?->id,
                'established_year' => 2015,
                'company_size' => '100-500',
                'contact_person' => 'Sarah Wijaya',
                'contact_person_phone' => '081234567890',
                'password' => Hash::make('password123'),
                'status' => 'aktif',
                'is_approved' => true,
                'is_verified' => true,
                'verified_at' => now(),
            ],
            [
                'company_name' => 'CV Kreatif Digital',
                'email' => 'recruitment@kreatifdigital.co.id',
                'phone' => '021-5555-0002',
                'address' => 'Jl. Gatot Subroto No. 456, Jakarta Selatan',
                'website' => 'https://kreatifdigital.co.id',
                'description' => 'Agensi kreatif yang bergerak di bidang digital marketing dan desain grafis.',
                'category_id' => $jurusans->where('nama', 'Multimedia')->first()?->id,
                'established_year' => 2018,
                'company_size' => '50-100',
                'contact_person' => 'Budi Santoso',
                'contact_person_phone' => '081234567891',
                'password' => Hash::make('password123'),
                'status' => 'aktif',
                'is_approved' => true,
                'is_verified' => true,
                'verified_at' => now(),
            ],
            [
                'company_name' => 'PT Otomotif Nusantara',
                'email' => 'karir@otomotifnusantara.com',
                'phone' => '021-5555-0003',
                'address' => 'Jl. Ahmad Yani No. 789, Surabaya',
                'website' => 'https://otomotifnusantara.com',
                'description' => 'Perusahaan manufacturing dan service otomotif dengan jaringan bengkel di seluruh Indonesia.',
                'category_id' => $jurusans->where('nama', 'Teknik Kendaraan Ringan')->first()?->id,
                'established_year' => 2010,
                'company_size' => '500+',
                'contact_person' => 'Ahmad Fauzi',
                'contact_person_phone' => '081234567892',
                'password' => Hash::make('password123'),
                'status' => 'aktif',
                'is_approved' => true,
                'is_verified' => true,
                'verified_at' => now(),
            ],
            [
                'company_name' => 'Toko Elektro Sejahtera',
                'email' => 'info@elektrosejahteta.com',
                'phone' => '021-5555-0004',
                'address' => 'Jl. Veteran No. 321, Malang',
                'website' => 'https://elektrosejahteta.com',
                'description' => 'Toko dan service elektronik yang melayani penjualan dan reparasi berbagai perangkat elektronik.',
                'category_id' => $jurusans->where('nama', 'Teknik Elektronika')->first()?->id,
                'established_year' => 2012,
                'company_size' => '10-50',
                'contact_person' => 'Siti Nurhaliza',
                'contact_person_phone' => '081234567893',
                'password' => Hash::make('password123'),
                'status' => 'aktif',
                'is_approved' => true,
                'is_verified' => true,
                'verified_at' => now(),
            ],
        ];

        foreach ($companies as $company) {
            Company::create($company);
        }
    }
}
