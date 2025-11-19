<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Company;
use App\Models\Notification;
use Illuminate\Support\Facades\Hash;

class TestCompanyRegistrationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:company-registration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test company registration notification';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Create test company
        $company = Company::create([
            'company_name' => 'PT Test Notification ' . now()->format('H:i:s'),
            'email' => 'testnotif' . time() . '@company.com',
            'phone' => '08123456789',
            'address' => 'Test Address',
            'contact_person' => 'Test Person',
            'contact_person_phone' => '08123456789',
            'password' => Hash::make('password'),
            'status' => 'pending',
        ]);

        $this->info('Company created successfully!');
        $this->info('Company ID: ' . $company->id);
        $this->info('Company Name: ' . $company->company_name);
        $this->info('Email: ' . $company->email);

        // Create notification
        try {
            $notification = Notification::createCompanyRegistration($company);
            $this->info('Notification created successfully!');
            $this->info('Notification ID: ' . $notification->id);
            $this->info('Notification Title: ' . $notification->title);
            $this->info('Notification Message: ' . $notification->message);
        } catch (\Exception $e) {
            $this->error('Error creating notification: ' . $e->getMessage());
        }

        $this->info('Total notifications in database: ' . Notification::count());
        $this->info('Total unread notifications: ' . Notification::unread()->count());

        return 0;
    }
}
