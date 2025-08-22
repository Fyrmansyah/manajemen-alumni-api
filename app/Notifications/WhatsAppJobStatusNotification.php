<?php

namespace App\Notifications;

use App\Jobs\SendWhatsAppNotificationJob;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WhatsAppJobStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected string $jobTitle,
        protected string $companyName,
        protected string $status,
    ) {}

    public function via($notifiable): array
    {
        return ['whatsapp'];
    }

    public function toWhatsApp($notifiable): string
    {
        $statusMessages = [
            'diterima' => '✅ *Selamat!* Lamaran Anda diterima',
            'accepted' => '✅ *Selamat!* Lamaran Anda diterima',
            'ditolak' => '❌ Lamaran Anda ditolak',
            'rejected' => '❌ Lamaran Anda ditolak',
            'interview' => '📋 Anda dipanggil untuk interview',
            'reviewed' => '👀 Lamaran Anda sedang direview',
            'review' => '👀 Lamaran Anda sedang direview',
            'submitted' => '📨 Lamaran Anda telah diterima sistem',
        ];

        $statusText = $statusMessages[$this->status] ?? 'Status lamaran Anda telah diperbarui';

        $message = "🔔 *Update Status Lamaran*\n\n";
        $message .= "{$statusText}\n\n";
        $message .= "Posisi: *{$this->jobTitle}*\n";
        $message .= "Perusahaan: *{$this->companyName}*\n\n";
        $message .= "Login ke sistem untuk melihat detail lengkap.\n";
        $message .= "🌐 " . config('app.url');

        $phoneNumber = $notifiable->phone ?? $notifiable->whatsapp_number;
        if ($phoneNumber) {
            SendWhatsAppNotificationJob::dispatch($phoneNumber, $message);
        }
        return $message;
    }
}
