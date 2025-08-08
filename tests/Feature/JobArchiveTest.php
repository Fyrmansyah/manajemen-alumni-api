<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobArchiveTest extends TestCase
{
    public function test_job_can_be_archived()
    {
        // Create a company first
        $company = Company::create([
            'company_name' => 'Test Company',
            'email' => 'test@company.com',
            'password' => bcrypt('password'),
            'phone' => '08123456789',
            'address' => 'Test Address',
            'industry' => 'Technology',
            'status' => 'active',
        ]);

        // Create a job
        $job = Job::create([
            'company_id' => $company->id,
            'title' => 'Test Job',
            'description' => 'Test job description',
            'requirements' => 'Test requirements',
            'location' => 'Jakarta',
            'type' => 'full_time',
            'application_deadline' => now()->addDays(30),
            'status' => 'active',
            'positions_available' => 1,
        ]);

        // Test archiving
        $reason = 'Test archive reason';
        $job->archive($reason);

        // Refresh the model to get updated data
        $job->refresh();

        // Assert the job is archived
        $this->assertTrue($job->isArchived());
        $this->assertEquals($reason, $job->archive_reason);
        $this->assertEquals('expired', $job->status);
        $this->assertNotNull($job->archived_at);

        echo "✅ Job archive test passed!\n";
        echo "Job ID: {$job->id}\n";
        echo "Archived at: {$job->archived_at}\n";
        echo "Archive reason: {$job->archive_reason}\n";
    }
}
