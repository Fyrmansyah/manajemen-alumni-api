<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Job;

class TestArchive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:archive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test job archive functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing job archive functionality...');

        // Get first active job
        $job = Job::active()->first();
        
        if (!$job) {
            $this->error('No active jobs found to test with.');
            return;
        }

        $this->info("Found job: {$job->title} (ID: {$job->id})");
        
        try {
            // Test archiving
            $reason = 'Test archive from command';
            $this->info("Attempting to archive job with reason: {$reason}");
            
            $job->archive($reason);
            $job->refresh();
            
            $this->info("Archive attempt completed.");
            $this->info("Job archived_at: " . ($job->archived_at ?? 'NULL'));
            $this->info("Job archive_reason: " . ($job->archive_reason ?? 'NULL'));
            $this->info("Job status: " . $job->status);
            $this->info("Is archived: " . ($job->isArchived() ? 'YES' : 'NO'));
            
            if ($job->isArchived()) {
                $this->info('✅ Archive test PASSED!');
                
                // Test unarchiving
                $this->info('Testing unarchive...');
                $job->unarchive();
                $job->refresh();
                
                $this->info("After unarchive - Is archived: " . ($job->isArchived() ? 'YES' : 'NO'));
                $this->info("After unarchive - Status: " . $job->status);
                
                if (!$job->isArchived()) {
                    $this->info('✅ Unarchive test PASSED!');
                } else {
                    $this->error('❌ Unarchive test FAILED!');
                }
            } else {
                $this->error('❌ Archive test FAILED!');
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Test failed with error: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }
    }
}
