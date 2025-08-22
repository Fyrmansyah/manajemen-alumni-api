<?php

namespace App\Notifications;

use App\Jobs\SendWhatsAppNotificationJob;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WhatsAppNewJobNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected string $jobTitle,
        protected string $companyName,
        protected string $location,
    ) {}

    public function via($notifiable): array
    {
        return ['whatsapp'];
    }

    public function toWhatsApp($notifiable): string
    {
        $message = "💼 *Lowongan Kerja Baru*\n\n";
        $message .= "Posisi: *{$this->jobTitle}*\n";
        $message .= "Perusahaan: *{$this->companyName}*\n";
        $message .= "Lokasi: *{$this->location}*\n\n";
        $message .= "Buruan lamar sebelum terlambat!\n";
        $message .= "🌐 " . config('app.url');

        $phoneNumber = $notifiable->phone ?? $notifiable->whatsapp_number;
        if ($phoneNumber) {
            SendWhatsAppNotificationJob::dispatch($phoneNumber, $message);
        }
        return $message;
    }
}
