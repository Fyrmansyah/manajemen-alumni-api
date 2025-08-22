<?php

namespace App\Notifications;

use App\Jobs\SendWhatsAppNotificationJob;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WhatsAppCompanyRegistrationNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected string $companyName,
        protected string $contactPerson,
    ) {}

    public function via($notifiable): array
    {
        return ['whatsapp'];
    }

    public function toWhatsApp($notifiable): string
    {
        $adminMessage = "🏢 *Pendaftaran Perusahaan Baru*\n\n";
        $adminMessage .= "Perusahaan: *{$this->companyName}*\n";
        $adminMessage .= "Kontak Person: *{$this->contactPerson}*\n\n";
        $adminMessage .= "Silakan verifikasi perusahaan di sistem admin.\n";
        $adminMessage .= "🌐 " . config('app.url') . "/admin";

        $companyMessage = "🏢 *Registrasi Berhasil di Website BKK*\n\n";
        $companyMessage .= "Halo *{$this->companyName}*,\n";
        $companyMessage .= "Terima kasih telah mendaftar di website BKK.\n";
        $companyMessage .= "Tim kami akan melakukan verifikasi data perusahaan Anda.\n\n";
        $companyMessage .= "Jika ada pertanyaan, silakan hubungi admin.\n";
        $companyMessage .= "🌐 " . config('app.url');

        $adminNumbers = config('services.fonnte.admin_numbers', []);
        foreach ($adminNumbers as $phoneNumber) {
            SendWhatsAppNotificationJob::dispatch($phoneNumber, $adminMessage);
        }

        $companyPhone = $notifiable->contact_person_phone ?? $notifiable->phone;
        if ($companyPhone) {
            SendWhatsAppNotificationJob::dispatch($companyPhone, $companyMessage);
        }
        return 'Notifikasi WhatsApp dikirim ke admin dan company.';
    }
}
