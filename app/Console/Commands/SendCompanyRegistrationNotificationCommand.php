<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Notifications\WhatsAppCompanyRegistrationNotification;
use Illuminate\Console\Command;

class SendCompanyRegistrationNotificationCommand extends Command
{
    protected $signature = 'whatsapp:send-company-notification {company_id}';
    protected $description = 'Send WhatsApp notification to company when registered';

    public function handle()
    {
        $companyId = $this->argument('company_id');
        $company = Company::find($companyId);

        if (!$company) {
            $this->error("Company with ID {$companyId} not found.");
            return 1;
        }

        // Pesan untuk admin
        $adminMessage = "\ud83c\udfe2 *Pendaftaran Perusahaan Baru*\n\n";
        $adminMessage .= "Perusahaan: *{$company->company_name}*\n";
        $adminMessage .= "Kontak Person: *{$company->contact_person}*\n\n";
        $adminMessage .= "Silakan verifikasi perusahaan di sistem admin.\n";
        $adminMessage .= "\ud83c\udf10 " . config('app.url') . "/admin";

        // Pesan untuk company
        $companyMessage = "\ud83c\udfe2 *Registrasi Berhasil di Website BKK*\n\n";
        $companyMessage .= "Halo *{$company->company_name}*,\n";
        $companyMessage .= "Terima kasih telah mendaftar di website BKK.\n";
        $companyMessage .= "Tim kami akan melakukan verifikasi data perusahaan Anda.\n";
        $companyMessage .= "\nJika ada pertanyaan, silakan hubungi admin.\n";
        $companyMessage .= "\ud83c\udf10 " . config('app.url');

        // Kirim ke admin
        $adminNumbers = config('services.fonnte.admin_numbers', []);
        foreach ($adminNumbers as $adminPhone) {
            \App\Jobs\SendWhatsAppNotificationJob::dispatch($adminPhone, $adminMessage);
        }

        // Kirim ke company
        $companyPhone = $company->contact_person_phone ?? $company->phone;
        \App\Jobs\SendWhatsAppNotificationJob::dispatch($companyPhone, $companyMessage);

        $this->info("WhatsApp notification sent to admin(s) and company: {$company->company_name} ({$companyPhone})");
        return 0;
    }
}