# WhatsApp Notification Integration dengan Fonnte.com

Aplikasi ini terintegrasi dengan layanan WhatsApp Fonnte.com untuk mengirim notifikasi push ke berbagai stakeholder sistem BKK.

## Setup

### 1. Konfigurasi Environment Variables

Tambahkan konfigurasi berikut di file `.env`:

```bash
# Fonnte WhatsApp Configuration
FONNTE_TOKEN=oGbgumYiost7Zm5aXs8gsgbUXXgKEfuSxFgwTYsW

# Admin WhatsApp Numbers (format: 62xxx tanpa +)
ADMIN_WHATSAPP_1=6282329613405


# Queue Configuration (untuk notifikasi asinkron)
QUEUE_CONNECTION=database
```

### 2. Menjalankan Queue Worker

Untuk memproses notifikasi WhatsApp secara asinkron:

```bash
php artisan queue:work
```

## Fitur Notifikasi WhatsApp

### 1. Notifikasi Registrasi Perusahaan
- **Trigger**: Saat perusahaan baru mendaftar
- **Penerima**: Admin BKK
- **Konten**: Informasi perusahaan baru yang mendaftar

### 2. Notifikasi Lamaran Kerja
- **Trigger**: Saat alumni mengirim lamaran
- **Penerima**: Perusahaan terkait
- **Konten**: Detail lamaran dan pelamar

### 3. Notifikasi Update Status Lamaran
- **Trigger**: Saat perusahaan mengupdate status lamaran
- **Penerima**: Alumni yang melamar
- **Konten**: Status terbaru lamaran (diterima, ditolak, interview, dll)

### 4. Notifikasi Lowongan Kerja Baru
- **Trigger**: Manual via command atau otomatis
- **Penerima**: Alumni yang relevan
- **Konten**: Detail lowongan kerja baru

### 5. Notifikasi Berita Terbaru
- **Trigger**: Manual via command
- **Penerima**: Semua alumni aktif
- **Konten**: Berita terbaru dari BKK

## Penggunaan Manual Command

### Mengirim Notifikasi Lowongan Kerja Baru

```bash
php artisan whatsapp:send-job-notification {job_id}
```

### Mengirim Notifikasi Berita

```bash
php artisan whatsapp:send-news-notification {news_id}
```

## Struktur Kode

### Services
- `App\Services\WhatsAppService`: Service utama untuk integrasi Fonnte API

### Jobs
- `App\Jobs\SendWhatsAppNotificationJob`: Job untuk mengirim notifikasi tunggal
- `App\Jobs\SendBulkWhatsAppNotificationJob`: Job untuk mengirim notifikasi massal

### Notifications
- `App\Notifications\WhatsAppNotifications`: Kumpulan class notification

### Channels
- `App\Channels\WhatsAppChannel`: Custom notification channel untuk WhatsApp

## API Fonnte.com

Aplikasi menggunakan API endpoint Fonnte.com:
- **URL**: `https://api.fonnte.com/send`
- **Method**: POST
- **Headers**: `Authorization: {token}`

### Format Nomor Telepon
- Format yang diterima: `62xxxxxxxxxxx` (tanpa tanda +)
- Contoh: `628123456789`

## Error Handling

- Semua error notifikasi akan di-log tetapi tidak mengganggu proses bisnis utama
- Job notification memiliki retry mechanism (3x dengan delay)
- Failed jobs akan disimpan dalam database untuk monitoring

## Monitoring

### Log Files
- Error dan success notification tercatat dalam log Laravel
- Lokasi: `storage/logs/laravel.log`

### Failed Jobs
Cek failed jobs dengan:
```bash
php artisan queue:failed
```

Retry failed jobs:
```bash
php artisan queue:retry all
```

## Customization

### Menambah Template Notifikasi Baru

1. Buat class notification baru di `app/Notifications/`
2. Implement method `toWhatsApp()`
3. Dispatch job di controller yang relevan

### Mengubah Format Pesan

Edit template pesan di masing-masing notification class atau service method.

### Menambah Kondisi Penerima

Customize logic di command atau service untuk menentukan siapa yang menerima notifikasi.

## Testing

Untuk testing di development, pastikan:
1. Token Fonnte valid dan aktif
2. Nomor WhatsApp admin sudah terdaftar di Fonnte
3. Queue worker berjalan
4. Log error untuk debugging

## Keamanan

- Token API Fonnte disimpan dalam environment variable
- Nomor telepon di-format dan divalidasi sebelum dikirim
- Rate limiting untuk mencegah spam (delay 1 detik antar pesan dalam bulk)
