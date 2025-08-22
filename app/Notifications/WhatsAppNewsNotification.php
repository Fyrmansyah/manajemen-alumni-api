<?php

namespace App\Notifications;

use App\Jobs\SendWhatsAppNotificationJob;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WhatsAppNewsNotification extends Notification
{
    use Queueable;

    public function __construct(protected string $newsTitle) {}

    public function via($notifiable): array
    {
        return ['whatsapp'];
    }

    public function toWhatsApp($notifiable): string
    {
        $message = "📰 *Berita Terbaru BKK*\n\n";
        $message .= "*{$this->newsTitle}*\n\n";
        $message .= "Baca selengkapnya di website BKK.\n";
        $message .= "🌐 " . config('app.url');

        $phoneNumber = $notifiable->phone ?? $notifiable->whatsapp_number;
        if ($phoneNumber) {
            SendWhatsAppNotificationJob::dispatch($phoneNumber, $message);
        }
        return $message;
    }
}
