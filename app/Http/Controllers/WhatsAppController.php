<?php

namespace App\Http\Controllers;

use App\Models\Alumni;
use App\Models\Company;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WhatsAppController extends Controller
{
    protected $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Get notification settings for alumni
     */
    public function getAlumniSettings()
    {
        $alumni = Auth::guard('alumni')->user();
        
        return response()->json([
            'success' => true,
            'data' => [
                'whatsapp_job_notifications' => $alumni->whatsapp_job_notifications ?? true,
                'whatsapp_news_notifications' => $alumni->whatsapp_news_notifications ?? true,
                'whatsapp_status_notifications' => $alumni->whatsapp_status_notifications ?? true,
                'whatsapp_number' => $alumni->whatsapp_number,
                'phone' => $alumni->phone,
            ]
        ]);
    }

    /**
     * Update notification settings for alumni
     */
    public function updateAlumniSettings(Request $request)
    {
        $request->validate([
            'whatsapp_job_notifications' => 'boolean',
            'whatsapp_news_notifications' => 'boolean',
            'whatsapp_status_notifications' => 'boolean',
            'whatsapp_number' => 'nullable|string|regex:/^(\+?62|0)[0-9]{9,13}$/',
        ]);

        $alumni = Auth::guard('alumni')->user();
        
        $alumni->update([
            'whatsapp_job_notifications' => $request->get('whatsapp_job_notifications', true),
            'whatsapp_news_notifications' => $request->get('whatsapp_news_notifications', true),
            'whatsapp_status_notifications' => $request->get('whatsapp_status_notifications', true),
            'whatsapp_number' => $request->whatsapp_number,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan notifikasi WhatsApp berhasil diperbarui'
        ]);
    }

    /**
     * Get notification settings for company
     */
    public function getCompanySettings()
    {
        $company = Auth::guard('company')->user();
        
        return response()->json([
            'success' => true,
            'data' => [
                'whatsapp_application_notifications' => $company->whatsapp_application_notifications ?? true,
                'contact_person_phone' => $company->contact_person_phone,
                'phone' => $company->phone,
            ]
        ]);
    }

    /**
     * Update notification settings for company
     */
    public function updateCompanySettings(Request $request)
    {
        $request->validate([
            'whatsapp_application_notifications' => 'boolean',
        ]);

        $company = Auth::guard('company')->user();
        
        $company->update([
            'whatsapp_application_notifications' => $request->get('whatsapp_application_notifications', true),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan notifikasi WhatsApp berhasil diperbarui'
        ]);
    }

    /**
     * Test WhatsApp notification (for admin)
     */
    public function testNotification(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string|regex:/^(\+?62|0)[0-9]{9,13}$/',
            'message' => 'required|string|max:500',
        ]);

        $result = $this->whatsAppService->sendMessage(
            $request->phone_number,
            $request->message
        );

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Pesan WhatsApp berhasil dikirim',
                'data' => $result['data']
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim pesan WhatsApp',
                'error' => $result['error']
            ], 500);
        }
    }

    /**
     * Send bulk notification to alumni (admin only)
     */
    public function sendBulkNotification(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
            'target_group' => 'required|in:all,active,by_jurusan',
            'jurusan_id' => 'required_if:target_group,by_jurusan|exists:jurusans,id',
        ]);

        $query = Alumni::where('status', 'active')
                      ->whereNotNull('phone')
                      ->where('phone', '!=', '')
                      ->where('whatsapp_job_notifications', true);

        if ($request->target_group === 'by_jurusan') {
            $query->where('jurusan_id', $request->jurusan_id);
        }

        $alumni = $query->get();
        $phoneNumbers = $alumni->pluck('phone')->filter()->toArray();

        if (empty($phoneNumbers)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada alumni yang memenuhi kriteria'
            ], 400);
        }

        // Dispatch bulk notification job
        \App\Jobs\SendBulkWhatsAppNotificationJob::dispatch($phoneNumbers, $request->message);

        return response()->json([
            'success' => true,
            'message' => "Notifikasi akan dikirim ke " . count($phoneNumbers) . " alumni",
            'total_recipients' => count($phoneNumbers)
        ]);
    }

    /**
     * Get notification history/stats (admin only)
     */
    public function getNotificationStats()
    {
        // You can implement this to show notification statistics
        // For now, return basic info
        return response()->json([
            'success' => true,
            'data' => [
                'total_alumni_with_whatsapp' => Alumni::whereNotNull('phone')->where('phone', '!=', '')->count(),
                'alumni_with_job_notifications_enabled' => Alumni::where('whatsapp_job_notifications', true)->count(),
                'alumni_with_news_notifications_enabled' => Alumni::where('whatsapp_news_notifications', true)->count(),
                'companies_with_notifications_enabled' => Company::where('whatsapp_application_notifications', true)->count(),
            ]
        ]);
    }
}
