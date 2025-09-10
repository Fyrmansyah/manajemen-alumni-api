@component('mail::message')
# Pendaftaran Perusahaan Diterima

Halo {{ $company->company_name }},

Terima kasih telah mendaftar di BKK SMKN 1 Surabaya. Akun perusahaan Anda saat ini berstatus Pending Verifikasi.

Tim kami akan memverifikasi data Anda dalam 1-2 hari kerja. Anda akan menerima notifikasi email saat proses verifikasi selesai.

@component('mail::button', ['url' => route('login')])
Masuk ke Akun
@endcomponent

Salam hangat,
Tim BKK SMKN 1 Surabaya
@endcomponent
