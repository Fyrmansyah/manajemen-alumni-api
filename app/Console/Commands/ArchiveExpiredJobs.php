<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Job;
use Carbon\Carbon;

class ArchiveExpiredJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jobs:archive-expired {--dry-run : Show what would be archived without actually doing it}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Archive jobs that have passed their application deadline';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        // Find expired jobs that haven't been archived yet
        $expiredJobs = Job::expiredButNotArchived()->get();
        
        if ($expiredJobs->count() === 0) {
            $this->info('No expired jobs found to archive.');
            return 0;
        }
        
        $this->info("Found {$expiredJobs->count()} expired job(s) to archive:");
        
        foreach ($expiredJobs as $job) {
            $daysOverdue = $job->application_deadline->diffInDays(now());
            
            $this->line("- ID: {$job->id} | {$job->title} | Company: {$job->company->company_name} | Overdue by: {$daysOverdue} days");
            
            if (!$isDryRun) {
                $job->archive("Automatically archived - deadline expired on {$job->application_deadline->format('d M Y')}");
                
                // Optionally notify the company
                $this->notifyCompany($job);
            }
        }
        
        if ($isDryRun) {
            $this->warn('DRY RUN: No jobs were actually archived. Remove --dry-run to perform the archiving.');
        } else {
            $this->info("Successfully archived {$expiredJobs->count()} job(s).");
        }
        
        return 0;
    }
    
    private function notifyCompany($job)
    {
        // Optionally send notification to company about archived job
        // This could be email, WhatsApp, or database notification
        try {
            // Example: Add to notifications table or send email
            // For now, just log it
            \Log::info("Job archived", [
                'job_id' => $job->id,
                'company_id' => $job->company_id,
                'title' => $job->title,
                'archived_at' => $job->archived_at
            ]);
        } catch (\Exception $e) {
            $this->warn("Could not notify company for job ID {$job->id}: {$e->getMessage()}");
        }
    }
}
