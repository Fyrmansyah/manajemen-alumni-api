@extends('layouts.app')

@section('title', 'Lowongan Kerja - BKK SMKN 1 Surabaya')

@section('content')
<div class="container mt-4 mb-5">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-primary text-white p-4 rounded">
                <h1 class="h2 mb-2">
                    <i class="fas fa-briefcase me-2"></i>Lowongan Kerja
                </h1>
                <p class="mb-0">Temukan peluang karir terbaik yang sesuai dengan keahlian dan minat Anda</p>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-filter me-2"></i>Filter Pencarian
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('jobs.index') }}" id="filterForm">
                        <!-- Search -->
                        <div class="mb-3">
                            <label for="search" class="form-label">Cari Pekerjaan</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="search" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Kata kunci...">
                        </div>

                        <!-- Location -->
                        <div class="mb-3">
                            <label for="location" class="form-label">Lokasi</label>
                            <select class="form-select" id="location" name="location">
                                <option value="">Semua Lokasi</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location }}" {{ request('location') == $location ? 'selected' : '' }}>
                                        {{ $location }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Job Type -->
                        <div class="mb-3">
                            <label for="type" class="form-label">Jenis Pekerjaan</label>
                            <select class="form-select" id="type" name="type">
                                <option value="">Semua Jenis</option>
                                @foreach($job_types as $key => $value)
                                    <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Salary Range -->
                        <div class="mb-3">
                            <label for="salary_min" class="form-label">Gaji Minimum</label>
                            <select class="form-select" id="salary_min" name="salary_min">
                                <option value="">Tidak ada minimum</option>
                                <option value="3000000" {{ request('salary_min') == '3000000' ? 'selected' : '' }}>Rp 3.000.000</option>
                                <option value="5000000" {{ request('salary_min') == '5000000' ? 'selected' : '' }}>Rp 5.000.000</option>
                                <option value="7000000" {{ request('salary_min') == '7000000' ? 'selected' : '' }}>Rp 7.000.000</option>
                                <option value="10000000" {{ request('salary_min') == '10000000' ? 'selected' : '' }}>Rp 10.000.000</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Cari
                            </button>
                            <a href="{{ route('jobs.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Jobs List -->
        <div class="col-lg-9">
            <!-- Results Summary -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <p class="text-muted mb-0">
                    Menampilkan {{ $jobs->firstItem() ?? 0 }}-{{ $jobs->lastItem() ?? 0 }} dari {{ $jobs->total() }} lowongan
                </p>
                <div class="d-flex align-items-center">
                    <label for="sort" class="form-label me-2 mb-0">Urutkan:</label>
                    <select class="form-select form-select-sm" id="sort" name="sort" style="width: auto;" onchange="document.getElementById('filterForm').submit();">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                        <option value="salary_high" {{ request('sort') == 'salary_high' ? 'selected' : '' }}>Gaji Tertinggi</option>
                        <option value="salary_low" {{ request('sort') == 'salary_low' ? 'selected' : '' }}>Gaji Terendah</option>
                    </select>
                </div>
            </div>

            <!-- Jobs Cards -->
            @forelse($jobs as $job)
                <div class="card shadow-sm mb-3 job-card">
                    <div class="card-body p-4">
                        <div class="row align-items-start">
                            <div class="col-auto">
                                @if(!empty($job->company->logo))
                                    <img src="{{ asset('storage/company_logos/' . $job->company->logo) }}" 
                                         alt="{{ $job->company->company_name }}" 
                                         class="rounded" 
                                         style="width: 60px; height: 60px; object-fit: cover;">
                                @else
                                    <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center" 
                                         style="width: 60px; height: 60px; font-size: 1.5rem;">
                                        {{ strtoupper(substr($job->company->company_name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="col">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h5 class="card-title mb-1">
                                            <a href="{{ route('jobs.show', $job->id) }}" class="text-decoration-none text-primary">
                                                {{ $job->title }}
                                            </a>
                                        </h5>
                                        <p class="text-muted mb-2">
                                            <i class="fas fa-building me-1"></i>{{ $job->company->company_name }}
                                        </p>
                                    </div>
                                    <div class="text-end">
                                        @if($job->salary_min && $job->salary_max)
                                            <p class="text-success fw-semibold mb-1">
                                                Rp {{ number_format($job->salary_min, 0, ',', '.') }} - 
                                                Rp {{ number_format($job->salary_max, 0, ',', '.') }}
                                            </p>
                                        @endif
                                        <small class="text-muted">
                                            {{ $job->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="job-meta d-flex flex-wrap gap-3 mb-2">
                                        <span class="text-muted">
                                            <i class="fas fa-map-marker-alt me-1 text-primary"></i>{{ $job->location }}
                                        </span>
                                        <span class="text-muted">
                                            <i class="fas fa-clock me-1 text-primary"></i>{{ ucfirst(str_replace('_', ' ', $job->type)) }}
                                        </span>
                                        <span class="text-muted">
                                            <i class="fas fa-users me-1 text-primary"></i>{{ $job->positions_available }} posisi
                                        </span>
                                        @if($job->applications_count > 0)
                                            <span class="text-muted">
                                                <i class="fas fa-user-check me-1 text-success"></i>{{ $job->applications_count }} pelamar
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-muted mb-0 job-description">
                                        {{ Str::limit(strip_tags($job->description), 120) }}
                                    </p>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="job-badges">
                                        <span class="badge bg-{{ $job->status === 'active' ? 'success' : ($job->status === 'closed' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($job->status) }}
                                        </span>
                                        @if($job->application_deadline)
                                            @php
                                                $daysLeft = now()->diffInDays($job->application_deadline, false);
                                                $isUrgent = $daysLeft <= 7 && $daysLeft >= 0;
                                                $isExpired = $daysLeft < 0;
                                            @endphp
                                            <small class="text-muted ms-2">
                                                <i class="fas fa-calendar-times me-1 {{ $isUrgent ? 'text-warning' : ($isExpired ? 'text-danger' : '') }}"></i>
                                                @if($isExpired)
                                                    <span class="text-danger">Expired</span>
                                                @elseif($isUrgent)
                                                    <span class="text-warning">{{ abs($daysLeft) }} hari lagi</span>
                                                @else
                                                    Deadline: {{ $job->application_deadline->format('d M Y') }}
                                                @endif
                                            </small>
                                        @endif
                                    </div>
                                    <div class="job-actions">
                                        <a href="{{ route('jobs.show', $job->id) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i>Detail
                                        </a>
                                        @auth
                                            @if(auth()->user()->role === 'alumni')
                                                @php
                                                    $hasApplied = false; // You can implement this check
                                                    $canApply = $job->status === 'active' && !$job->isExpired();
                                                @endphp
                                                @if($hasApplied)
                                                    <button class="btn btn-secondary btn-sm ms-2" disabled>
                                                        <i class="fas fa-check me-1"></i>Sudah Melamar
                                                    </button>
                                                @elseif($canApply)
                                                    <button class="btn btn-primary btn-sm ms-2" onclick="applyJob({{ $job->id }})">
                                                        <i class="fas fa-paper-plane me-1"></i>Lamar
                                                    </button>
                                                @else
                                                    <button class="btn btn-secondary btn-sm ms-2" disabled>
                                                        <i class="fas fa-times me-1"></i>Tidak Tersedia
                                                    </button>
                                                @endif
                                            @endif
                                        @else
                                            <a href="{{ route('login') }}" class="btn btn-primary btn-sm ms-2">
                                                <i class="fas fa-sign-in-alt me-1"></i>Login untuk Melamar
                                            </a>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Tidak ada lowongan ditemukan</h4>
                    <p class="text-muted">Coba ubah kriteria pencarian Anda</p>
                    <a href="{{ route('jobs.index') }}" class="btn btn-primary">
                        <i class="fas fa-refresh me-2"></i>Lihat Semua Lowongan
                    </a>
                </div>
            @endforelse

            <!-- Pagination -->
            @if($jobs->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $jobs->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-submit form when sort changes
document.getElementById('sort').addEventListener('change', function() {
    // Add sort parameter to filter form
    const filterForm = document.getElementById('filterForm');
    const sortInput = document.createElement('input');
    sortInput.type = 'hidden';
    sortInput.name = 'sort';
    sortInput.value = this.value;
    filterForm.appendChild(sortInput);
    filterForm.submit();
});

// Apply for job function
@auth
function applyJob(jobId) {
    if (confirm('Apakah Anda yakin ingin melamar pekerjaan ini?')) {
        fetch(`/api/jobs/${jobId}/apply`, {
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
</script>
@endpush

@section('styles')
<style>
.job-card {
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    overflow: hidden;
}

.job-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(0,0,0,0.15) !important;
    border-color: #007bff;
}

.card-title a {
    text-decoration: none;
    color: #2c3e50;
    font-weight: 600;
    transition: color 0.3s ease;
}

.card-title a:hover {
    text-decoration: none !important;
    color: #007bff;
}

.form-select-sm {
    font-size: 0.875rem;
    border-radius: 8px;
    border: 1px solid #dee2e6;
    transition: border-color 0.3s ease;
}

.form-select-sm:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.job-meta-item {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 8px;
    padding: 12px 16px;
    border-left: 4px solid #dee2e6;
    transition: all 0.3s ease;
    margin-bottom: 8px;
}

.job-meta-item:hover {
    background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
    border-left-color: #007bff;
    transform: translateX(3px);
}

.badge-custom {
    font-size: 0.75rem;
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.btn-outline-primary {
    border-radius: 25px;
    padding: 0.5rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
    border-width: 2px;
}

.btn-outline-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
}

.btn-success {
    border-radius: 25px;
    padding: 0.5rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border: none;
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
    background: linear-gradient(135deg, #218838 0%, #1e7e34 100%);
}

.text-primary-custom {
    color: #007bff !important;
}

.text-success-custom {
    color: #28a745 !important;
}

.text-warning-custom {
    color: #ffc107 !important;
}

.text-danger-custom {
    color: #dc3545 !important;
}

.card-body {
    padding: 2rem;
}

.card-header h5 {
    margin: 0;
    font-weight: 600;
}

.icon-wrapper {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    flex-shrink: 0;
}

.icon-company {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.icon-location {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.icon-type {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
}

.icon-salary {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    color: white;
}

.icon-applications {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    color: white;
}

.job-card-header {
    border-bottom: 3px solid #f8f9fa;
    padding-bottom: 1.5rem;
    margin-bottom: 1.5rem;
}

.filter-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    border: 1px solid #dee2e6;
}

.filter-section .form-control,
.filter-section .form-select {
    border-radius: 8px;
    border: 1px solid #ced4da;
    transition: all 0.3s ease;
}

.filter-section .form-control:focus,
.filter-section .form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.search-results-info {
    background: #e3f2fd;
    border: 1px solid #bbdefb;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1.5rem;
    color: #1976d2;
}

.text-truncate-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.6;
}

.job-status-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    z-index: 10;
}

.card {
    position: relative;
}

@media (max-width: 768px) {
    .card-body {
        padding: 1.5rem;
    }
    
    .job-meta-item {
        padding: 10px 12px;
        margin-bottom: 6px;
    }
    
    .icon-wrapper {
        width: 35px;
        height: 35px;
        margin-right: 10px;
    }
    
    .btn-outline-primary,
    .btn-success {
        padding: 0.4rem 1rem;
        font-size: 0.875rem;
    }
}
</style>
@endsection
