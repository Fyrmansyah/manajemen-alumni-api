@component('mail::message')
# Akun Perusahaan Telah Diverifikasi

Selamat {{ $company->company_name }},

Akun perusahaan Anda telah berhasil diverifikasi. Sekarang Anda dapat mengakses semua fitur perusahaan seperti membuat dan mengelola lowongan pekerjaan.

@component('mail::button', ['url' => route('company.dashboard')])
Buka Dashboard Perusahaan
@endcomponent

Terima kasih telah bergabung dengan BKK SMKN 1 Surabaya.

Salam,
Tim BKK SMKN 1 Surabaya
@endcomponent
