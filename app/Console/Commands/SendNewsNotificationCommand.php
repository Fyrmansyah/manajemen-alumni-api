<?php

namespace App\Console\Commands;

use App\Models\Alumni;
use App\Models\News;
use App\Jobs\SendBulkWhatsAppNotificationJob;
use Illuminate\Console\Command;

class SendNewsNotificationCommand extends Command
{
    protected $signature = 'whatsapp:send-news-notification {news_id}';
    protected $description = 'Send WhatsApp notification about news to alumni';

    public function handle()
    {
        $newsId = $this->argument('news_id');
        $news = News::find($newsId);

        if (!$news) {
            $this->error("News with ID {$newsId} not found.");
            return 1;
        }

        // Get all active alumni
        $alumni = Alumni::where('status', 'active')
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->get();

        if ($alumni->isEmpty()) {
            $this->info('No alumni found to notify.');
            return 0;
        }

        $phoneNumbers = $alumni->pluck('phone')->filter()->toArray();

        $message = "📰 *Berita Terbaru BKK*\n\n";
        $message .= "*{$news->title}*\n\n";
        $message .= "Baca selengkapnya di website BKK.\n";
        $message .= "🌐 " . config('app.url');

        // Dispatch bulk notification job
        SendBulkWhatsAppNotificationJob::dispatch($phoneNumbers, $message);

        $this->info("WhatsApp notification sent to " . count($phoneNumbers) . " alumni about news: {$news->title}");
        return 0;
    }
}
