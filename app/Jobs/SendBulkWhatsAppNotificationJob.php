<?php

namespace App\Jobs;

use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendBulkWhatsAppNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $phoneNumbers;
    protected $message;
    protected $type;

    public $tries = 3;
    public $backoff = [10, 30, 60];

    /**
     * Create a new job instance.
     */
    public function __construct(array $phoneNumbers, $message, $type = 'text')
    {
        $this->phoneNumbers = $phoneNumbers;
        $this->message = $message;
        $this->type = $type;
    }

    /**
     * Execute the job.
     */
    public function handle(WhatsAppService $whatsAppService)
    {
        try {
            $successCount = 0;
            $failureCount = 0;

            foreach ($this->phoneNumbers as $phoneNumber) {
                try {
                    $result = $whatsAppService->sendMessage($phoneNumber, $this->message);
                    
                    if ($result['success']) {
                        $successCount++;
                    } else {
                        $failureCount++;
                        Log::warning('Failed to send WhatsApp message in bulk', [
                            'phone' => $phoneNumber,
                            'error' => $result['error']
                        ]);
                    }

                    // Add small delay between messages to avoid rate limiting
                    sleep(1);

                } catch (\Exception $e) {
                    $failureCount++;
                    Log::error('Error sending WhatsApp message in bulk', [
                        'phone' => $phoneNumber,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            Log::info('Bulk WhatsApp notification completed', [
                'total' => count($this->phoneNumbers),
                'success' => $successCount,
                'failure' => $failureCount
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk WhatsApp notification job failed', [
                'error' => $e->getMessage(),
                'attempt' => $this->attempts()
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception)
    {
        Log::error('Bulk WhatsApp notification job failed permanently', [
            'phone_count' => count($this->phoneNumbers),
            'error' => $exception->getMessage()
        ]);
    }
}
