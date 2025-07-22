@extends('layouts.app')

@section('title', $job->title . ' - BKK SMKN 1 Surabaya')

@section('content')
<div class="container mt-4 mb-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('jobs.index') }}">Lowongan Kerja</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($job->title, 30) }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body p-4">
                    <!-- Job Header -->
                    <div class="row align-items-start mb-4">
                        <div class="col-auto">
                            @if(!empty($job->company->logo))
                                <img src="{{ asset('storage/company_logos/' . $job->company->logo) }}" 
                                     alt="{{ $job->company->company_name }}" 
                                     class="rounded" 
                                     style="width: 80px; height: 80px; object-fit: cover;">
                            @else
                                <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center" 
                                     style="width: 80px; height: 80px; font-size: 2rem;">
                                    {{ strtoupper(substr($job->company->company_name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="col">
                            <h1 class="h2 mb-2">{{ $job->title }}</h1>
                            <p class="text-muted mb-2">
                                <i class="fas fa-building me-2"></i>{{ $job->company->company_name }}
                            </p>
                            <div class="row text-muted">
                                <div class="col-md-6">
                                    <p class="mb-1">
                                        <i class="fas fa-map-marker-alt me-2"></i>{{ $job->location }}
                                    </p>
                                    <p class="mb-1">
                                        <i class="fas fa-clock me-2"></i>{{ ucfirst(str_replace('_', ' ', $job->type)) }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1">
                                        <i class="fas fa-users me-2"></i>{{ $job->positions_available }} posisi tersedia
                                    </p>
                                    <p class="mb-1">
                                        <i class="fas fa-calendar me-2"></i>{{ $job->created_at->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Job Details -->
                    <div class="mb-4">
                        <h4>Deskripsi Pekerjaan</h4>
                        <div class="job-description">
                            {!! nl2br(e($job->description)) !!}
                        </div>
                    </div>

                    @if($job->requirements)
                    <div class="mb-4">
                        <h4>Persyaratan</h4>
                        <div class="job-requirements">
                            {!! nl2br(e($job->requirements)) !!}
                        </div>
                    </div>
                    @endif

                    @if($job->benefits)
                    <div class="mb-4">
                        <h4>Keuntungan & Fasilitas</h4>
                        <div class="job-benefits">
                            {!! nl2br(e($job->benefits)) !!}
                        </div>
                    </div>
                    @endif

                    <!-- Company Info -->
                    <div class="border-top pt-4">
                        <h4>Tentang Perusahaan</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Nama Perusahaan:</strong> {{ $job->company->company_name }}</p>
                                <p><strong>Alamat:</strong> {{ $job->company->address }}</p>
                                @if($job->company->phone)
                                    <p><strong>Telepon:</strong> {{ $job->company->phone }}</p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                @if($job->company->email)
                                    <p><strong>Email:</strong> {{ $job->company->email }}</p>
                                @endif
                                @if($job->company->website)
                                    <p><strong>Website:</strong> 
                                        <a href="{{ $job->company->website }}" target="_blank" class="text-primary">
                                            {{ $job->company->website }}
                                        </a>
                                    </p>
                                @endif
                                @if($job->company->industry)
                                    <p><strong>Industri:</strong> {{ $job->company->industry }}</p>
                                @endif
                            </div>
                        </div>
                        @if($job->company->description)
                            <p class="mt-3">{{ $job->company->description }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Related Jobs -->
            @if($relatedJobs->count() > 0)
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Lowongan Terkait</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($relatedJobs as $relatedJob)
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3 h-100">
                                <h6>
                                    <a href="{{ route('jobs.show', $relatedJob->id) }}" class="text-decoration-none">
                                        {{ $relatedJob->title }}
                                    </a>
                                </h6>
                                <p class="text-muted small mb-1">{{ $relatedJob->company->company_name }}</p>
                                <p class="text-muted small mb-0">
                                    <i class="fas fa-map-marker-alt me-1"></i>{{ $relatedJob->location }}
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Apply Section -->
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    @if($job->salary_min && $job->salary_max)
                        <h4 class="text-success mb-3">
                            Rp {{ number_format($job->salary_min, 0, ',', '.') }} - 
                            Rp {{ number_format($job->salary_max, 0, ',', '.') }}
                        </h4>
                    @endif

                    @auth('alumni')
                        @if($hasApplied)
                            <div class="alert alert-info">
                                <i class="fas fa-check-circle me-2"></i>
                                Anda sudah melamar pekerjaan ini
                            </div>
                            <a href="{{ route('alumni.applications') }}" class="btn btn-outline-primary">
                                <i class="fas fa-eye me-2"></i>Lihat Status Lamaran
                            </a>
                        @else
                            <button class="btn btn-primary btn-lg w-100 mb-3" onclick="applyJob({{ $job->id }})">
                                <i class="fas fa-paper-plane me-2"></i>Lamar Sekarang
                            </button>
                        @endif
                    @elseauth('admin')
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle me-2"></i>
                            Admin tidak dapat melamar pekerjaan
                        </div>
                    @elseauth('company')
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle me-2"></i>
                            Perusahaan tidak dapat melamar pekerjaan
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg w-100 mb-3">
                            <i class="fas fa-sign-in-alt me-2"></i>Login untuk Melamar
                        </a>
                        <p class="small text-muted">
                            Belum punya akun? 
                            <a href="{{ route('register') }}">Daftar di sini</a>
                        </p>
                    @endauth

                    <div class="d-grid gap-2 mt-3">
                        <button class="btn btn-outline-secondary" onclick="shareJob()">
                            <i class="fas fa-share-alt me-2"></i>Bagikan
                        </button>
                        <button class="btn btn-outline-secondary" onclick="saveJob({{ $job->id }})">
                            <i class="fas fa-bookmark me-2"></i>Simpan
                        </button>
                    </div>
                </div>
            </div>

            <!-- Job Info -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Informasi Lowongan</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-5"><strong>Status:</strong></div>
                        <div class="col-7">
                            <span class="badge bg-{{ $job->status === 'active' ? 'success' : 'secondary' }}">
                                {{ ucfirst($job->status) }}
                            </span>
                        </div>
                    </div>
                    @if($job->application_deadline)
                    <div class="row mb-2">
                        <div class="col-5"><strong>Deadline:</strong></div>
                        <div class="col-7">{{ $job->application_deadline->format('d M Y') }}</div>
                    </div>
                    @endif
                    <div class="row mb-2">
                        <div class="col-5"><strong>Diposting:</strong></div>
                        <div class="col-7">{{ $job->created_at->format('d M Y') }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5"><strong>Pelamar:</strong></div>
                        <div class="col-7">{{ $job->applications()->count() }} orang</div>
                    </div>
                    <div class="row">
                        <div class="col-5"><strong>ID Lowongan:</strong></div>
                        <div class="col-7">#{{ $job->id }}</div>
                    </div>
                </div>
            </div>

            <!-- Tips -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-lightbulb me-2"></i>Tips Melamar
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Baca deskripsi pekerjaan dengan teliti
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Pastikan kualifikasi Anda sesuai
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Siapkan CV dan portfolio terbaru
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success me-2"></i>
                            Tulis surat lamaran yang menarik
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Apply for job function
@auth
function applyJob(jobId) {
    // Show apply modal with form
    const coverLetter = prompt('Masukkan cover letter Anda (wajib diisi):');
    
    if (!coverLetter || coverLetter.trim() === '') {
        alert('Cover letter harus diisi!');
        return;
    }

    if (confirm('Apakah Anda yakin ingin melamar pekerjaan ini?')) {
        const formData = new FormData();
        formData.append('cover_letter', coverLetter);
        
        fetch(`/jobs/${jobId}/apply`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Lamaran berhasil dikirim!');
                location.reload();
            } else {
                alert(data.message || 'Terjadi kesalahan saat mengirim lamaran');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengirim lamaran');
        });
    }
}
@endauth

// Share job function
function shareJob() {
    if (navigator.share) {
        navigator.share({
            title: '{{ $job->title }}',
            text: 'Lowongan kerja di {{ $job->company->company_name }}',
            url: window.location.href
        });
    } else {
        // Fallback to copying URL to clipboard
        navigator.clipboard.writeText(window.location.href).then(function() {
            alert('Link berhasil disalin ke clipboard!');
        });
    }
}

// Save job function
@auth
function saveJob(jobId) {
    fetch(`/api/jobs/${jobId}/save`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Authorization': 'Bearer ' + localStorage.getItem('auth_token')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Lowongan berhasil disimpan!');
        } else {
            alert(data.message || 'Terjadi kesalahan saat menyimpan lowongan');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyimpan lowongan');
    });
}
@endauth
</script>
@endpush

@section('styles')
<style>
.job-description,
.job-requirements,
.job-benefits {
    line-height: 1.6;
}

.job-description ul,
.job-requirements ul,
.job-benefits ul {
    padding-left: 1.5rem;
}

.card-header h6 {
    color: #495057;
}
</style>
@endsection
