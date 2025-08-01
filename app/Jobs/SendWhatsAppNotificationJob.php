<?php

namespace App\Jobs;

use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWhatsAppNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $phoneNumber;
    protected $message;
    protected $type;
    protected $imageUrl;

    public $tries = 3; // Maximum retry attempts
    public $backoff = [10, 30, 60]; // Retry delay in seconds

    /**
     * Create a new job instance.
     */
    public function __construct($phoneNumber, $message, $type = 'text', $imageUrl = null)
    {
        $this->phoneNumber = $phoneNumber;
        $this->message = $message;
        $this->type = $type;
        $this->imageUrl = $imageUrl;
    }

    /**
     * Execute the job.
     */
    public function handle(WhatsAppService $whatsAppService)
    {
        try {
            if ($this->type === 'image' && $this->imageUrl) {
                $result = $whatsAppService->sendImageMessage(
                    $this->phoneNumber,
                    $this->message,
                    $this->imageUrl
                );
            } else {
                $result = $whatsAppService->sendMessage(
                    $this->phoneNumber,
                    $this->message
                );
            }

            if (!$result['success']) {
                throw new \Exception('Failed to send WhatsApp message: ' . $result['error']);
            }

            Log::info('WhatsApp notification sent successfully via job', [
                'phone' => $this->phoneNumber,
                'type' => $this->type
            ]);

        } catch (\Exception $e) {
            Log::error('WhatsApp notification job failed', [
                'phone' => $this->phoneNumber,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts()
            ]);

            // Re-throw the exception to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception)
    {
        Log::error('WhatsApp notification job failed permanently', [
            'phone' => $this->phoneNumber,
            'message' => $this->message,
            'error' => $exception->getMessage()
        ]);
    }
}
