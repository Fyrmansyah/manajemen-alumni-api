<?php

namespace App\Notifications;

use App\Jobs\SendWhatsAppNotificationJob;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WhatsAppJobApplicationNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected string $jobTitle,
        protected string $applicantName,
        protected string $companyName,
    ) {}

    public function via($notifiable): array
    {
        return ['whatsapp'];
    }

    public function toWhatsApp($notifiable): string
    {
        $message = "🔔 *Notifikasi Lamaran Kerja*\n\n";
        $message .= "Ada lamaran baru untuk posisi: *{$this->jobTitle}*\n";
        $message .= "Pelamar: *{$this->applicantName}*\n";
        $message .= "Perusahaan: *{$this->companyName}*\n\n";
        $message .= "Silakan login ke sistem untuk melihat detail lamaran.\n";
        $message .= "🌐 " . config('app.url');

        $phoneNumber = $notifiable->contact_person_phone ?? $notifiable->phone;
        if ($phoneNumber) {
            SendWhatsAppNotificationJob::dispatch($phoneNumber, $message);
        }
        return $message;
    }
}
