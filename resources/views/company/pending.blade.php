@extends('layouts.app')

@section('title', 'Menunggu Verifikasi')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <i class="fas fa-hourglass-half fa-3x text-warning mb-3"></i>
                        <h3 class="mb-2">Akun Perusahaan Menunggu Verifikasi</h3>
                        <p class="text-muted mb-2">Terima kasih telah mendaftar. Tim admin kami akan memverifikasi data Anda dalam 1–2 hari kerja.</p>
                        @php
                            $joinedAt = auth('company')->user()?->created_at;
                            $estimate = $joinedAt ? \Carbon\Carbon::parse($joinedAt)->addWeekdays(2) : null;
                        @endphp
                        @if($estimate)
                            <p class="small text-muted">Perkiraan selesai: <strong>{{ $estimate->format('d M Y') }}</strong></p>
                        @endif
                    </div>
                    <div class="border rounded p-3 bg-light">
                        <p class="mb-1"><strong>Status:</strong> Pending Verifikasi</p>
                        <p class="mb-3">Anda akan menerima email saat akun Anda telah diverifikasi.</p>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary"><i class="fas fa-home me-1"></i>Beranda</a>
                            <div>
                                <a href="{{ route('company.profile') }}" class="btn btn-outline-primary me-2"><i class="fas fa-user-cog me-1"></i>Lengkapi Profil</a>
                                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-danger"><i class="fas fa-sign-out-alt me-1"></i>Logout</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="text-center text-muted small mt-3">
        Jika membutuhkan bantuan, hubungi admin BKK.
    </div>
</div>
@endsection
