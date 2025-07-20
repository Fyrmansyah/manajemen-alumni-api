<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Job;
use App\Models\Company;
use Carbon\Carbon;

class JobPostingSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();
        
        $jobPostings = [
            [
                'company_id' => $companies->first()->id,
                'title' => 'Full Stack Developer',
                'description' => 'Kami mencari Full Stack Developer yang berpengalaman dalam pengembangan aplikasi web menggunakan PHP Laravel dan JavaScript. Kandidat ideal memiliki pemahaman yang baik tentang front-end dan back-end development.',
                'requirements' => 'Minimal D3/S1 Teknik Informatika atau Teknik Komputer, Pengalaman 1-2 tahun, Menguasai PHP Laravel, JavaScript, HTML/CSS, MySQL, Git',
                'location' => 'Jakarta',
                'type' => 'full_time',
                'salary_min' => 8000000,
                'salary_max' => 12000000,
                'application_deadline' => Carbon::now()->addDays(30),
                'status' => 'active',
                'positions_available' => 2,
            ],
            [
                'company_id' => $companies->skip(1)->first()->id,
                'title' => 'Graphic Designer',
                'description' => 'Posisi untuk Graphic Designer kreatif yang akan bertanggung jawab untuk membuat desain visual yang menarik untuk berbagai media digital dan cetak.',
                'requirements' => 'Minimal SMK/D3 Desain Grafis atau Multimedia, Portfolio yang menarik, Menguasai Adobe Creative Suite, Kreatif dan detail oriented',
                'location' => 'Jakarta',
                'type' => 'full_time',
                'salary_min' => 5000000,
                'salary_max' => 8000000,
                'application_deadline' => Carbon::now()->addDays(25),
                'status' => 'active',
                'positions_available' => 1,
            ],
            [
                'company_id' => $companies->skip(2)->first()->id,
                'title' => 'Teknisi Otomotif',
                'description' => 'Dicari teknisi otomotif yang berpengalaman untuk melakukan perawatan dan perbaikan kendaraan. Kandidat akan bekerja di bengkel resmi dengan peralatan modern.',
                'requirements' => 'Minimal SMK Teknik Kendaraan Ringan, Pengalaman 1 tahun di bidang otomotif, Memahami sistem mesin dan kelistrikan mobil, Dapat bekerja dalam tim',
                'location' => 'Surabaya',
                'type' => 'full_time',
                'salary_min' => 4500000,
                'salary_max' => 7000000,
                'application_deadline' => Carbon::now()->addDays(20),
                'status' => 'active',
                'positions_available' => 3,
            ],
            [
                'company_id' => $companies->skip(3)->first()->id,
                'title' => 'Teknisi Elektronik',
                'description' => 'Lowongan untuk teknisi elektronik yang akan bertugas melakukan service dan reparasi berbagai perangkat elektronik seperti TV, kulkas, dan perangkat rumah tangga lainnya.',
                'requirements' => 'Minimal SMK Teknik Elektronika, Menguasai dasar-dasar elektronika, Dapat menggunakan multimeter dan alat ukur elektronik, Teliti dan sabar',
                'location' => 'Malang',
                'type' => 'full_time',
                'salary_min' => 3500000,
                'salary_max' => 5500000,
                'application_deadline' => Carbon::now()->addDays(15),
                'status' => 'active',
                'positions_available' => 2,
            ],
            [
                'company_id' => $companies->first()->id,
                'title' => 'Junior Network Administrator',
                'description' => 'Posisi entry level untuk fresh graduate yang tertarik mengembangkan karir di bidang network administration. Training akan diberikan untuk kandidat terpilih.',
                'requirements' => 'Fresh graduate SMK/D3 Teknik Komputer dan Jaringan, Memahami dasar-dasar networking, Familiar dengan Windows Server, Eager to learn',
                'location' => 'Jakarta',
                'type' => 'full_time',
                'salary_min' => 4000000,
                'salary_max' => 6000000,
                'application_deadline' => Carbon::now()->addDays(35),
                'status' => 'active',
                'positions_available' => 1,
            ],
        ];

        foreach ($jobPostings as $job) {
            Job::create($job);
        }
    }
}
