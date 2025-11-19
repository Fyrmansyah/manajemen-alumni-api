<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notification;

class TestNotificationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create test notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Create test notification
        $notification = Notification::create([
            'type' => 'company_registered',
            'title' => 'Test Perusahaan Baru Terdaftar',
            'message' => 'Perusahaan PT Test Indonesia telah mendaftar dan menunggu verifikasi.',
            'data' => [
                'company_id' => 999,
                'company_name' => 'PT Test Indonesia',
                'company_email' => 'test@company.com',
                'action_url' => '#'
            ],
            'icon' => 'fas fa-building',
            'color' => 'warning',
            'is_read' => false
        ]);

        $this->info('Test notification created successfully!');
        $this->info('ID: ' . $notification->id);
        $this->info('Title: ' . $notification->title);
        $this->info('Message: ' . $notification->message);
        $this->info('Created at: ' . $notification->created_at);

        // Create another test notification  
        $notification2 = Notification::create([
            'type' => 'job_application',
            'title' => 'Lamaran Kerja Baru',
            'message' => 'Ada lamaran baru untuk posisi Software Developer.',
            'data' => [
                'application_id' => 888,
                'job_title' => 'Software Developer',
                'applicant_name' => 'John Doe',
                'action_url' => '#'
            ],
            'icon' => 'fas fa-user-tie',
            'color' => 'info',
            'is_read' => false
        ]);

        $this->info('Second test notification created successfully!');
        $this->info('ID: ' . $notification2->id);
        $this->info('Title: ' . $notification2->title);

        $this->info('Total unread notifications: ' . Notification::unread()->count());
        
        return 0;
    }
}
