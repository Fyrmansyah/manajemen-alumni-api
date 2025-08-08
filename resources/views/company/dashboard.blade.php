@extends('layouts.app')

@section('title', 'Dashboard Perusahaan - BKK SMKN 1 Surabaya')

@section('content')
<div class="container-fluid mt-4 mb-5">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Dashboard Perusahaan</h1>
                    <p class="text-muted mb-0">Selamat datang, {{ auth()->user()->name }}</p>
                </div>
                <div>
                    <span class="badge bg-success">Verified Partner</span>
                    <small class="text-muted ms-2">{{ now()->format('d M Y, H:i') }} WIB</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Lowongan
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_jobs'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-briefcase fa-2x text-gray-300"></i>
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
                                Lowongan Aktif
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_jobs'] }}</div>
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
                                Menunggu Review
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
                        <a href="{{ route('company.jobs.create') }}" class="list-group-item list-group-item-action border-0 d-flex align-items-center">
                            <i class="fas fa-plus-circle text-success me-3"></i>
                            <div>
                                <div class="fw-bold">Posting Lowongan</div>
                                <small class="text-muted">Buat lowongan kerja baru</small>
                            </div>
                        </a>
                        <a href="{{ route('company.applications') }}" class="list-group-item list-group-item-action border-0 d-flex align-items-center">
                            <i class="fas fa-users text-info me-3"></i>
                            <div>
                                <div class="fw-bold">Kelola Lamaran</div>
                                <small class="text-muted">Review kandidat yang melamar</small>
                            </div>
                        </a>
                        <a href="{{ route('company.jobs') }}" class="list-group-item list-group-item-action border-0 d-flex align-items-center">
                            <i class="fas fa-briefcase text-warning me-3"></i>
                            <div>
                                <div class="fw-bold">Kelola Lowongan</div>
                                <small class="text-muted">Edit atau tutup lowongan</small>
                            </div>
                        </a>
                        <a href="{{ route('company.profile') }}" class="list-group-item list-group-item-action border-0 d-flex align-items-center">
                            <i class="fas fa-building text-primary me-3"></i>
                            <div>
                                <div class="fw-bold">Profil Perusahaan</div>
                                <small class="text-muted">Update informasi perusahaan</small>
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
                        <i class="fas fa-user-tie me-2"></i>Lamaran Terbaru
                    </h6>
                    <a href="{{ route('company.applications') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                <div class="card-body">
                    @forelse($recentApplications as $application)
                        <div class="d-flex align-items-start mb-3 {{ !$loop->last ? 'border-bottom pb-3' : '' }}">
                            <div class="me-3">
                                <div class="bg-info text-white rounded d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px;">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">{{ $application->alumni->name }}</div>
                                <div class="text-muted small">{{ $application->job->title }}</div>
                                <div class="text-muted small">
                                    <i class="fas fa-calendar me-1"></i>{{ $application->created_at->format('d M Y') }}
                                    @if($application->alumni->graduation_year)
                                        <span class="ms-2">
                                            <i class="fas fa-graduation-cap me-1"></i>Lulus {{ $application->alumni->graduation_year }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-{{ $application->status === 'pending' ? 'warning' : ($application->status === 'accepted' ? 'success' : 'danger') }}">
                                    {{ ucfirst($application->status) }}
                                </span>
                                <div class="mt-1">
                                    <a href="{{ route('company.applications') }}?application={{ $application->id }}" class="btn btn-outline-primary btn-sm">
                                        Review
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="fas fa-user-tie fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada lamaran masuk</h5>
                            <p class="text-muted">Posting lowongan untuk mulai menerima lamaran dari alumni</p>
                            <a href="{{ route('company.jobs') }}?action=create" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Buat Lowongan
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Jobs -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-briefcase me-2"></i>Lowongan Terbaru
                    </h6>
                    <a href="{{ route('company.jobs') }}" class="btn btn-sm btn-outline-primary">Kelola Semua Lowongan</a>
                </div>
                <div class="card-body">
                    @forelse($recentJobs as $job)
                        <div class="row align-items-center mb-3 {{ !$loop->last ? 'border-bottom pb-3' : '' }}">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 40px;">
                                            <i class="fas fa-briefcase"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">{{ $job->title }}</h6>
                                        <p class="text-muted small mb-0">
                                            <i class="fas fa-map-marker-alt me-1"></i>{{ $job->location }}
                                            <span class="ms-2">
                                                <i class="fas fa-users me-1"></i>{{ $job->positions_available }} posisi
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <span class="badge bg-{{ $job->status === 'active' ? 'success' : 'secondary' }} fs-6">
                                    {{ ucfirst($job->status) }}
                                </span>
                                <div class="text-muted small mt-1">
                                    {{ $job->applications_count ?? 0 }} pelamar
                                </div>
                            </div>
                            <div class="col-md-3 text-end">
                                <div class="text-muted small mb-1">{{ $job->created_at->format('d M Y') }}</div>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('jobs.show', $job->id) }}" class="btn btn-outline-primary" target="_blank">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('company.jobs') }}?edit={{ $job->id }}" class="btn btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($job->status === 'active')
                                        <button class="btn btn-outline-danger" onclick="closeJob({{ $job->id }}, '{{ $job->title }}')" title="Tutup Lowongan">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada lowongan</h5>
                            <p class="text-muted">Mulai posting lowongan untuk menjangkau talenta terbaik dari SMKN 1 Surabaya</p>
                            <a href="{{ route('company.jobs') }}?action=create" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Buat Lowongan Pertama
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function closeJob(jobId, jobTitle) {
    if (confirm(`Apakah Anda yakin ingin menutup lowongan "${jobTitle}"?`)) {
        // Create a form to submit the close request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/company/jobs/${jobId}/close`;
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfToken);
        
        // Add method override
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'PATCH';
        form.appendChild(methodField);
        
        // Submit the form
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush

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

.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}
</style>
@endsection
