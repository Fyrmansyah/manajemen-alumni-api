@extends('layouts.app')

@section('title', 'Dashboard Admin - BKK SMKN 1 Surabaya')

@section('content')
<div class="container-fluid mt-4 mb-5">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Dashboard Admin</h1>
                    <p class="text-muted mb-0">Selamat datang, {{ auth('admin')->user() ? auth('admin')->user()->nama : 'Admin' }}</p>
                </div>
                <div>
                    <span class="badge bg-success">Online</span>
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
                                Total Alumni
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_alumni'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                                Total Perusahaan
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_companies'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
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
                                Lowongan Aktif
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_jobs'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-briefcase fa-2x text-gray-300"></i>
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
                                Total Lamaran
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_applications'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
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
                        <a href="{{ route('admin.jobs.create') }}" class="list-group-item list-group-item-action border-0 d-flex align-items-center">
                            <i class="fas fa-plus-circle text-success me-3"></i>
                            <div>
                                <div class="fw-bold">Tambah Lowongan</div>
                                <small class="text-muted">Buat lowongan kerja baru</small>
                            </div>
                        </a>
                        <a href="{{ route('admin.news.create') }}" class="list-group-item list-group-item-action border-0 d-flex align-items-center">
                            <i class="fas fa-newspaper text-info me-3"></i>
                            <div>
                                <div class="fw-bold">Tulis Berita</div>
                                <small class="text-muted">Publikasikan berita terbaru</small>
                            </div>
                        </a>
                        <a href="{{ route('admin.companies.index') }}" class="list-group-item list-group-item-action border-0 d-flex align-items-center">
                            <i class="fas fa-building text-warning me-3"></i>
                            <div>
                                <div class="fw-bold">Kelola Perusahaan</div>
                                <small class="text-muted">Verifikasi & kelola perusahaan</small>
                            </div>
                        </a>
                        <a href="{{ route('admin.alumni.index') }}" class="list-group-item list-group-item-action border-0 d-flex align-items-center">
                            <i class="fas fa-users text-primary me-3"></i>
                            <div>
                                <div class="fw-bold">Kelola Alumni</div>
                                <small class="text-muted">Manajemen data alumni</small>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history me-2"></i>Aktivitas Terbaru
                    </h6>
                    <a href="#" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                @forelse($recentActivities as $activity)
                                <tr>
                                    <td style="width: 50px;">
                                        <div class="rounded-circle bg-{{ $activity['color'] }} text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-{{ $activity['icon'] }}"></i>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $activity['title'] }}</div>
                                        <div class="text-muted small">{{ $activity['description'] }}</div>
                                    </td>
                                    <td class="text-end">
                                        <small class="text-muted">{{ $activity['time'] }}</small>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-2x mb-2"></i>
                                        <div>Belum ada aktivitas terbaru</div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Monthly Applications Chart -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-line me-2"></i>Statistik Lamaran Bulanan
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="applicationsChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Companies -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-trophy me-2"></i>Top Perusahaan
                    </h6>
                </div>
                <div class="card-body">
                    @forelse($topCompanies as $company)
                    <div class="d-flex align-items-center mb-3 {{ !$loop->last ? 'border-bottom pb-3' : '' }}">
                        <div class="me-3">
                            @if($company->logo)
                                <img src="{{ asset('storage/company_logos/' . $company->logo) }}" 
                                     alt="{{ $company->company_name }}" 
                                     class="rounded" 
                                     style="width: 40px; height: 40px; object-fit: cover;">
                            @else
                                <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px;">
                                    {{ strtoupper(substr($company->company_name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold">{{ $company->company_name }}</div>
                            <small class="text-muted">{{ $company->jobs_count }} lowongan</small>
                        </div>
                        <div>
                            <span class="badge bg-primary">{{ $company->applications_count ?? 0 }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-building fa-2x mb-2"></i>
                        <div>Belum ada data perusahaan</div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Jobs -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-briefcase me-2"></i>Lowongan Terbaru
                    </h6>
                    <a href="{{ route('admin.jobs.index') }}" class="btn btn-sm btn-outline-primary">Kelola</a>
                </div>
                <div class="card-body">
                    @forelse($recentJobs as $job)
                    <div class="d-flex align-items-start mb-3 {{ !$loop->last ? 'border-bottom pb-3' : '' }}">
                        <div class="me-3">
                            <div class="bg-info text-white rounded d-flex align-items-center justify-content-center" 
                                 style="width: 40px; height: 40px;">
                                <i class="fas fa-briefcase"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold">{{ $job->title }}</div>
                            <div class="text-muted small">{{ $job->company->company_name }}</div>
                            <div class="text-muted small">
                                <i class="fas fa-map-marker-alt me-1"></i>{{ $job->location }}
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-{{ $job->status === 'active' ? 'success' : 'secondary' }}">
                                {{ ucfirst($job->status) }}
                            </span>
                            <div class="text-muted small mt-1">{{ $job->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-briefcase fa-2x mb-2"></i>
                        <div>Belum ada lowongan</div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Applications -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-file-alt me-2"></i>Lamaran Terbaru
                    </h6>
                    <a href="{{ route('admin.applications.index') }}" class="btn btn-sm btn-outline-primary">Kelola</a>
                </div>
                <div class="card-body">
                    @forelse($recentApplications as $application)
                    <div class="d-flex align-items-start mb-3 {{ !$loop->last ? 'border-bottom pb-3' : '' }}">
                        <div class="me-3">
                            <div class="bg-warning text-white rounded d-flex align-items-center justify-content-center" 
                                 style="width: 40px; height: 40px;">
                                <i class="fas fa-user"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold">{{ $application->alumni->name }}</div>
                            <div class="text-muted small">{{ $application->job->title }}</div>
                            <div class="text-muted small">{{ $application->job->company->company_name }}</div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-{{ $application->status === 'pending' ? 'warning' : ($application->status === 'accepted' ? 'success' : 'danger') }}">
                                {{ ucfirst($application->status) }}
                            </span>
                            <div class="text-muted small mt-1">{{ $application->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-file-alt fa-2x mb-2"></i>
                        <div>Belum ada lamaran</div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Applications Chart
const ctx = document.getElementById('applicationsChart').getContext('2d');
const applicationsChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($chartData['months']),
        datasets: [{
            label: 'Jumlah Lamaran',
            data: @json($chartData['applications']),
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
            title: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Auto refresh dashboard data every 5 minutes
setInterval(function() {
    location.reload();
}, 300000);
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
</style>
@endsection
