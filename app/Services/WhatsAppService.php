<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private $apiUrl;
    private $token;

    public function __construct()
    {
        $this->apiUrl = 'https://api.fonnte.com/send';
        $this->token = config('services.fonnte.token');
    }

    /**
     * Send WhatsApp message
     */
    public function sendMessage($target, $message, $type = 'text')
    {
        try {
            $payload = [
                'target' => $this->formatPhoneNumber($target),
                'message' => $message,
                'countryCode' => '62', // Indonesia country code
            ];

            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post($this->apiUrl, $payload);

            if ($response->successful()) {
                $responseData = $response->json();
                Log::info('WhatsApp message sent successfully', [
                    'target' => $target,
                    'response' => $responseData
                ]);
                return [
                    'success' => true,
                    'data' => $responseData
                ];
            } else {
                Log::error('Failed to send WhatsApp message', [
                    'target' => $target,
                    'response' => $response->body(),
                    'status' => $response->status()
                ]);
                return [
                    'success' => false,
                    'error' => $response->body()
                ];
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp service error', [
                'target' => $target,
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send WhatsApp message with image
     */
    public function sendImageMessage($target, $message, $imageUrl)
    {
        try {
            $payload = [
                'target' => $this->formatPhoneNumber($target),
                'message' => $message,
                'url' => $imageUrl,
                'countryCode' => '62',
            ];

            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post($this->apiUrl, $payload);

            if ($response->successful()) {
                $responseData = $response->json();
                Log::info('WhatsApp image message sent successfully', [
                    'target' => $target,
                    'response' => $responseData
                ]);
                return [
                    'success' => true,
                    'data' => $responseData
                ];
            } else {
                Log::error('Failed to send WhatsApp image message', [
                    'target' => $target,
                    'response' => $response->body(),
                    'status' => $response->status()
                ]);
                return [
                    'success' => false,
                    'error' => $response->body()
                ];
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp image service error', [
                'target' => $target,
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send notification to admin
     */
    public function sendAdminNotification($message)
    {
        $adminNumbers = config('services.fonnte.admin_numbers', []);
        
        $results = [];
        foreach ($adminNumbers as $number) {
            $results[] = $this->sendMessage($number, $message);
        }
        
        return $results;
    }

    /**
     * Format phone number for WhatsApp
     */
    private function formatPhoneNumber($phoneNumber)
    {
        // Remove all non-numeric characters
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Remove leading zero if exists
        if (substr($phoneNumber, 0, 1) === '0') {
            $phoneNumber = substr($phoneNumber, 1);
        }
        
        // Add country code if not exists
        if (substr($phoneNumber, 0, 2) !== '62') {
            $phoneNumber = '62' . $phoneNumber;
        }
        
        return $phoneNumber;
    }

    /**
     * Send job application notification to company
     */
    public function sendJobApplicationNotification($companyPhone, $jobTitle, $applicantName)
    {
        $message = "🔔 *Notifikasi Lamaran Kerja*\n\n";
        $message .= "Ada lamaran baru untuk posisi: *{$jobTitle}*\n";
        $message .= "Pelamar: *{$applicantName}*\n\n";
        $message .= "Silakan login ke sistem untuk melihat detail lamaran.\n";
        $message .= "🌐 " . config('app.url');

        return $this->sendMessage($companyPhone, $message);
    }

    /**
     * Send job status update notification to alumni
     */
    public function sendJobStatusNotification($alumniPhone, $jobTitle, $companyName, $status)
    {
        $statusMessages = [
            'diterima' => '✅ *Selamat!* Lamaran Anda diterima',
            'ditolak' => '❌ Lamaran Anda ditolak',
            'interview' => '📋 Anda dipanggil untuk interview',
            'review' => '👀 Lamaran Anda sedang direview'
        ];

        $statusText = $statusMessages[$status] ?? 'Status lamaran Anda telah diperbarui';

        $message = "🔔 *Update Status Lamaran*\n\n";
        $message .= "{$statusText}\n\n";
        $message .= "Posisi: *{$jobTitle}*\n";
        $message .= "Perusahaan: *{$companyName}*\n\n";
        $message .= "Login ke sistem untuk melihat detail lengkap.\n";
        $message .= "🌐 " . config('app.url');

        return $this->sendMessage($alumniPhone, $message);
    }

    /**
     * Send company registration notification to admin
     */
    public function sendCompanyRegistrationNotification($companyName, $contactPerson)
    {
        $message = "🏢 *Registrasi Perusahaan Baru*\n\n";
        $message .= "Perusahaan: *{$companyName}*\n";
        $message .= "Kontak Person: *{$contactPerson}*\n\n";
        $message .= "Silakan verifikasi perusahaan di sistem admin.\n";
        $message .= "🌐 " . config('app.url') . "/admin";

        return $this->sendAdminNotification($message);
    }

    /**
     * Send new job posting notification to relevant alumni
     */
    public function sendNewJobNotification($alumniPhones, $jobTitle, $companyName, $location)
    {
        $message = "💼 *Lowongan Kerja Baru*\n\n";
        $message .= "Posisi: *{$jobTitle}*\n";
        $message .= "Perusahaan: *{$companyName}*\n";
        $message .= "Lokasi: *{$location}*\n\n";
        $message .= "Buruan lamar sebelum terlambat!\n";
        $message .= "🌐 " . config('app.url');

        $results = [];
        foreach ($alumniPhones as $phone) {
            $results[] = $this->sendMessage($phone, $message);
        }

        return $results;
    }

    /**
     * Send news notification
     */
    public function sendNewsNotification($phones, $newsTitle)
    {
        $message = "📰 *Berita Terbaru BKK*\n\n";
        $message .= "*{$newsTitle}*\n\n";
        $message .= "Baca selengkapnya di website BKK.\n";
        $message .= "🌐 " . config('app.url');

        $results = [];
        foreach ($phones as $phone) {
            $results[] = $this->sendMessage($phone, $message);
        }

        return $results;
    }
}
