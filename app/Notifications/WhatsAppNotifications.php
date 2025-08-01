<?php

namespace App\Notifications;

use App\Jobs\SendWhatsAppNotificationJob;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WhatsAppJobApplicationNotification extends Notification
{
    use Queueable;

    protected $jobTitle;
    protected $applicantName;
    protected $companyName;

    public function __construct($jobTitle, $applicantName, $companyName)
    {
        $this->jobTitle = $jobTitle;
        $this->applicantName = $applicantName;
        $this->companyName = $companyName;
    }

    public function via($notifiable)
    {
        return ['whatsapp'];
    }

    public function toWhatsApp($notifiable)
    {
        $message = "🔔 *Notifikasi Lamaran Kerja*\n\n";
        $message .= "Ada lamaran baru untuk posisi: *{$this->jobTitle}*\n";
        $message .= "Pelamar: *{$this->applicantName}*\n";
        $message .= "Perusahaan: *{$this->companyName}*\n\n";
        $message .= "Silakan login ke sistem untuk melihat detail lamaran.\n";
        $message .= "🌐 " . config('app.url');

        // Get phone number from notifiable (Company model)
        $phoneNumber = $notifiable->contact_person_phone ?? $notifiable->phone;

        // Dispatch job to send WhatsApp message
        SendWhatsAppNotificationJob::dispatch($phoneNumber, $message);

        return $message;
    }
}

class WhatsAppJobStatusNotification extends Notification
{
    use Queueable;

    protected $jobTitle;
    protected $companyName;
    protected $status;

    public function __construct($jobTitle, $companyName, $status)
    {
        $this->jobTitle = $jobTitle;
        $this->companyName = $companyName;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['whatsapp'];
    }

    public function toWhatsApp($notifiable)
    {
        $statusMessages = [
            'diterima' => '✅ *Selamat!* Lamaran Anda diterima',
            'ditolak' => '❌ Lamaran Anda ditolak',
            'interview' => '📋 Anda dipanggil untuk interview',
            'review' => '👀 Lamaran Anda sedang direview'
        ];

        $statusText = $statusMessages[$this->status] ?? 'Status lamaran Anda telah diperbarui';

        $message = "🔔 *Update Status Lamaran*\n\n";
        $message .= "{$statusText}\n\n";
        $message .= "Posisi: *{$this->jobTitle}*\n";
        $message .= "Perusahaan: *{$this->companyName}*\n\n";
        $message .= "Login ke sistem untuk melihat detail lengkap.\n";
        $message .= "🌐 " . config('app.url');

        // Get phone number from notifiable (Alumni model)
        $phoneNumber = $notifiable->phone ?? $notifiable->whatsapp_number;

        // Dispatch job to send WhatsApp message
        SendWhatsAppNotificationJob::dispatch($phoneNumber, $message);

        return $message;
    }
}

class WhatsAppCompanyRegistrationNotification extends Notification
{
    use Queueable;

    protected $companyName;
    protected $contactPerson;

    public function __construct($companyName, $contactPerson)
    {
        $this->companyName = $companyName;
        $this->contactPerson = $contactPerson;
    }

    public function via($notifiable)
    {
        return ['whatsapp'];
    }

    public function toWhatsApp($notifiable)
    {
        $message = "🏢 *Registrasi Perusahaan Baru*\n\n";
        $message .= "Perusahaan: *{$this->companyName}*\n";
        $message .= "Kontak Person: *{$this->contactPerson}*\n\n";
        $message .= "Silakan verifikasi perusahaan di sistem admin.\n";
        $message .= "🌐 " . config('app.url') . "/admin";

        // Get admin phone numbers from config
        // Pesan untuk admin
        $adminMessage = "\ud83c\udfe2 *Pendaftaran Perusahaan Baru*\n\n";
        $adminMessage .= "Perusahaan: *{$this->companyName}*\n";
        $adminMessage .= "Kontak Person: *{$this->contactPerson}*\n\n";
        $adminMessage .= "Silakan verifikasi perusahaan di sistem admin.\n";
        $adminMessage .= "\ud83c\udf10 " . config('app.url') . "/admin";

        // Pesan untuk company
        $companyMessage = "\ud83c\udfe2 *Registrasi Berhasil di Website BKK*\n\n";
        $companyMessage .= "Halo *{$this->companyName}*,\n";
        $companyMessage .= "Terima kasih telah mendaftar di website BKK.\n";
        $companyMessage .= "Tim kami akan melakukan verifikasi data perusahaan Anda.\n";
        $companyMessage .= "\nJika ada pertanyaan, silakan hubungi admin.\n";
        $companyMessage .= "\ud83c\udf10 " . config('app.url');

        // Kirim ke admin
        $adminNumbers = config('services.fonnte.admin_numbers', []);
        foreach ($adminNumbers as $phoneNumber) {
            SendWhatsAppNotificationJob::dispatch($phoneNumber, $adminMessage);
        }

        // Kirim ke company (notifiable)
        $companyPhone = $notifiable->contact_person_phone ?? $notifiable->phone;
        SendWhatsAppNotificationJob::dispatch($companyPhone, $companyMessage);

        return 'Notifikasi WhatsApp dikirim ke admin dan company.';
    }
}

class WhatsAppNewJobNotification extends Notification
{
    use Queueable;

    protected $jobTitle;
    protected $companyName;
    protected $location;

    public function __construct($jobTitle, $companyName, $location)
    {
        $this->jobTitle = $jobTitle;
        $this->companyName = $companyName;
        $this->location = $location;
    }

    public function via($notifiable)
    {
        return ['whatsapp'];
    }

    public function toWhatsApp($notifiable)
    {
        $message = "💼 *Lowongan Kerja Baru*\n\n";
        $message .= "Posisi: *{$this->jobTitle}*\n";
        $message .= "Perusahaan: *{$this->companyName}*\n";
        $message .= "Lokasi: *{$this->location}*\n\n";
        $message .= "Buruan lamar sebelum terlambat!\n";
        $message .= "🌐 " . config('app.url');

        // Get phone number from notifiable (Alumni model)
        $phoneNumber = $notifiable->phone ?? $notifiable->whatsapp_number;

        // Dispatch job to send WhatsApp message
        SendWhatsAppNotificationJob::dispatch($phoneNumber, $message);

        return $message;
    }
}

class WhatsAppNewsNotification extends Notification
{
    use Queueable;

    protected $newsTitle;

    public function __construct($newsTitle)
    {
        $this->newsTitle = $newsTitle;
    }

    public function via($notifiable)
    {
        return ['whatsapp'];
    }

    public function toWhatsApp($notifiable)
    {
        $message = "📰 *Berita Terbaru BKK*\n\n";
        $message .= "*{$this->newsTitle}*\n\n";
        $message .= "Baca selengkapnya di website BKK.\n";
        $message .= "🌐 " . config('app.url');

        // Get phone number from notifiable
        $phoneNumber = $notifiable->phone ?? $notifiable->whatsapp_number;

        // Dispatch job to send WhatsApp message
        SendWhatsAppNotificationJob::dispatch($phoneNumber, $message);

        return $message;
    }
}
