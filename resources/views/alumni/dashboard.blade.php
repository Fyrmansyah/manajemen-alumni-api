@extends('layouts.app')

@section('title', 'Dashboard Alumni - BKK SMKN 1 Surabaya')

@section('content')
<div class="container-fluid mt-4 mb-5">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Dashboard Alumni</h1>
                    <p class="text-muted mb-0">Selamat datang, {{ auth('alumni')->user() ? auth('alumni')->user()->nama : 'Alumni' }}</p>
                </div>
                <div>
                    <span class="badge bg-success">Alumni Aktif</span>
                    <small class="text-muted ms-2">{{ now()->format('d M Y, H:i') }} WIB</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Lamaran
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_applications'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Menunggu Respons
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending_applications'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Diterima
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['accepted_applications'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Lowongan Tersedia
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['available_jobs'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-briefcase fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Quick Actions -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-rocket me-2"></i>Aksi Cepat
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('jobs.index') }}" class="list-group-item list-group-item-action border-0 d-flex align-items-center">
                            <i class="fas fa-search text-primary me-3"></i>
                            <div>
                                <div class="fw-bold">Cari Lowongan</div>
                                <small class="text-muted">Temukan pekerjaan yang sesuai</small>
                            </div>
                        </a>
                        <a href="{{ route('alumni.applications') }}" class="list-group-item list-group-item-action border-0 d-flex align-items-center">
                            <i class="fas fa-clipboard-list text-warning me-3"></i>
                            <div>
                                <div class="fw-bold">Status Lamaran</div>
                                <small class="text-muted">Cek progress lamaran Anda</small>
                            </div>
                        </a>
                        <a href="{{ route('alumni.profile') }}" class="list-group-item list-group-item-action border-0 d-flex align-items-center">
                            <i class="fas fa-user-edit text-info me-3"></i>
                            <div>
                                <div class="fw-bold">Edit Profil</div>
                                <small class="text-muted">Perbarui data diri Anda</small>
                            </div>
                        </a>
                        <a href="{{ route('news.index') }}" class="list-group-item list-group-item-action border-0 d-flex align-items-center">
                            <i class="fas fa-newspaper text-success me-3"></i>
                            <div>
                                <div class="fw-bold">Baca Berita</div>
                                <small class="text-muted">Info terbaru dunia kerja</small>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Applications -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history me-2"></i>Lamaran Terbaru
                    </h6>
                    <a href="{{ route('alumni.applications') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                <div class="card-body">
                    @forelse($recentApplications as $application)
                        <div class="d-flex align-items-start mb-3 {{ !$loop->last ? 'border-bottom pb-3' : '' }}">
                            <div class="me-3">
                                @if($application->job->company->logo)
                                    <img src="{{ asset('storage/company_logos/' . $application->job->company->logo) }}" 
                                         alt="{{ $application->job->company->company_name }}" 
                                         class="rounded" 
                                         style="width: 40px; height: 40px; object-fit: cover;">
                                @else
                                    <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px;">
                                        {{ strtoupper(substr($application->job->company->company_name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">{{ $application->job->title }}</div>
                                <div class="text-muted small">{{ $application->job->company->company_name }}</div>
                                <div class="text-muted small">
                                    <i class="fas fa-calendar me-1"></i>{{ $application->created_at->format('d M Y') }}
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-{{ $application->status === 'pending' ? 'warning' : ($application->status === 'accepted' ? 'success' : 'danger') }}">
                                    {{ ucfirst($application->status) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada lamaran</h5>
                            <p class="text-muted">Mulai mencari dan melamar pekerjaan yang sesuai dengan keahlian Anda</p>
                            <a href="{{ route('jobs.index') }}" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Cari Lowongan
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Recommended Jobs -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-star me-2"></i>Lowongan Rekomendasi
                    </h6>
                    <a href="{{ route('jobs.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua Lowongan</a>
                </div>
                <div class="card-body">
                    <div class="row">
                        @forelse($recommendedJobs as $job)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start mb-3">
                                            <div class="me-3">
                                                @if($job->company->logo)
                                                    <img src="{{ asset('storage/company_logos/' . $job->company->logo) }}" 
                                                         alt="{{ $job->company->company_name }}" 
                                                         class="rounded" 
                                                         style="width: 40px; height: 40px; object-fit: cover;">
                                                @else
                                                    <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center" 
                                                         style="width: 40px; height: 40px;">
                                                        {{ strtoupper(substr($job->company->company_name, 0, 1)) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="card-title mb-1">{{ $job->title }}</h6>
                                                <p class="text-muted small mb-0">{{ $job->company->company_name }}</p>
                                            </div>
                                        </div>
                                        
                                        <p class="text-muted small mb-2">
                                            <i class="fas fa-map-marker-alt me-1"></i>{{ $job->location }}
                                        </p>
                                        
                                        @if($job->salary_min && $job->salary_max)
                                            <p class="text-success small mb-3">
                                                <i class="fas fa-money-bill-wave me-1"></i>
                                                Rp {{ number_format($job->salary_min, 0, ',', '.') }} - Rp {{ number_format($job->salary_max, 0, ',', '.') }}
                                            </p>
                                        @endif
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">{{ $job->created_at->diffForHumans() }}</small>
                                            <a href="{{ route('jobs.show', $job->id) }}" class="btn btn-outline-primary btn-sm">
                                                Lihat Detail
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="text-center py-4">
                                    <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Belum ada rekomendasi lowongan</h5>
                                    <p class="text-muted">Lengkapi profil Anda untuk mendapatkan rekomendasi yang lebih baik</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.text-xs {
    font-size: 0.7rem;
}

.list-group-item:hover {
    background-color: #f8f9fa;
}

.card {
    border: 0;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}
</style>
@endsection
