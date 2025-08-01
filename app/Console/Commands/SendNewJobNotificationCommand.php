<?php

namespace App\Console\Commands;

use App\Models\Job;
use App\Models\Alumni;
use App\Jobs\SendBulkWhatsAppNotificationJob;
use Illuminate\Console\Command;

class SendNewJobNotificationCommand extends Command
{
    protected $signature = 'whatsapp:send-job-notification {job_id}';
    protected $description = 'Send WhatsApp notification about new job posting to relevant alumni';

    public function handle()
    {
        $jobId = $this->argument('job_id');
        $job = Job::with('company')->find($jobId);

        if (!$job) {
            $this->error("Job with ID {$jobId} not found.");
            return 1;
        }

        // Get alumni who might be interested (you can customize this logic)
        $relevantAlumni = Alumni::where('status', 'active')
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->get();

        if ($relevantAlumni->isEmpty()) {
            $this->info('No relevant alumni found to notify.');
            return 0;
        }

        $phoneNumbers = $relevantAlumni->pluck('phone')->filter()->toArray();

        $message = "💼 *Lowongan Kerja Baru*\n\n";
        $message .= "Posisi: *{$job->title}*\n";
        $message .= "Perusahaan: *{$job->company->company_name}*\n";
        $message .= "Lokasi: *{$job->location}*\n\n";
        $message .= "Buruan lamar sebelum terlambat!\n";
        $message .= "🌐 " . config('app.url');

        // Dispatch bulk notification job
        SendBulkWhatsAppNotificationJob::dispatch($phoneNumbers, $message);

        $this->info("WhatsApp notification sent to " . count($phoneNumbers) . " alumni about job: {$job->title}");
        return 0;
    }
}
