<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Job;
use App\Models\Application;
use App\Models\Alumni;
use App\Models\News;
use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class JobApplicationSeeder extends Seeder
{
    public function run(): void
    {
        // Create some companies
        $companies = Company::factory(10)->active()->create();
        
        // Create some pending companies
        Company::factory(5)->pending()->create();

        // Create jobs for active companies
        foreach ($companies as $company) {
            Job::factory(rand(2, 5))->active()->create([
                'company_id' => $company->id
            ]);
        }

        // Create some draft jobs
        Job::factory(10)->draft()->create();

        // Create applications from existing alumni
        $alumni = Alumni::take(20)->get();
        $activeJobs = Job::active()->get();

        foreach ($alumni as $alumnus) {
            // Each alumni applies for 1-3 jobs
            $jobsToApply = $activeJobs->random(rand(1, 3));
            
            foreach ($jobsToApply as $job) {
                Application::create([
                    'alumni_id' => $alumnus->id,
                    'job_posting_id' => $job->id,
                    'cover_letter' => 'Saya sangat tertarik untuk bergabung dengan ' . $job->company->company_name . ' sebagai ' . $job->title . '. Dengan latar belakang pendidikan di ' . $alumnus->jurusan->nama . ', saya yakin dapat berkontribusi positif untuk perusahaan.',
                    'status' => collect(['submitted', 'reviewed', 'interview', 'accepted', 'rejected'])->random(),
                    'applied_at' => now()->subDays(rand(1, 30)),
                ]);
            }
        }

        // Create some news articles
        $admin = Admin::first();
        if ($admin) {
            for ($i = 1; $i <= 10; $i++) {
                News::create([
                    'title' => 'Berita BKK SMKN 1 Surabaya #' . $i,
                    'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
                    'slug' => Str::slug('Berita BKK SMKN 1 Surabaya #' . $i),
                    'status' => 'published',
                    'author_id' => $admin->id,
                    'published_at' => now()->subDays(rand(1, 60)),
                    'views' => rand(10, 500),
                ]);
            }
        }
    }
}
