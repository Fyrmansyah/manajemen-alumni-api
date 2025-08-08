@extends('layouts.app')

@section('title', 'Kelola Lowongan - BKK SMKN 1 Surabaya')

@section('content')
<div class="container-fluid mt-4 mb-5">

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Kelola Lowongan</h1>
                    <p class="text-muted mb-0">Kelola semua lowongan kerja yang telah diposting</p>
                </div>
                <div>
                    <a href="{{ route('company.jobs.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Buat Lowongan Baru
                    </a>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jobs->total() }}</div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jobs->where('status', 'active')->count() }}</div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jobs->sum('applications_count') }}</div>
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
                                Lowongan Ditutup
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jobs->where('status', 'closed')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-lock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Jobs List -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list me-2"></i>Daftar Lowongan
                    </h6>
                </div>
                <div class="card-body">
                    @if($jobs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Judul Lowongan</th>
                                        <th>Lokasi</th>
                                        <th>Tipe</th>
                                        <th>Status</th>
                                        <th>Lamaran</th>
                                        <th>Deadline</th>
                                        <th>Dibuat</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($jobs as $job)
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>{{ $job->title }}</strong>
                                                @if($job->salary_min && $job->salary_max)
                                                    <br>
                                                    <small class="text-muted">
                                                        Rp {{ number_format($job->salary_min) }} - {{ number_format($job->salary_max) }}
                                                    </small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $job->location }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $jobTypes[$job->type] ?? $job->type }}</span>
                                        </td>
                                        <td>
                                            @if($job->status === 'active')
                                                <span class="badge bg-success">Aktif</span>
                                            @elseif($job->status === 'closed')
                                                <span class="badge bg-danger">Ditutup</span>
                                            @else
                                                <span class="badge bg-warning">{{ ucfirst($job->status) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $job->applications_count }} lamaran</span>
                                        </td>
                                        <td>
                                            @if($job->application_deadline)
                                                <small class="text-muted">
                                                    {{ \Carbon\Carbon::parse($job->application_deadline)->format('d/m/Y') }}
                                                    @if(\Carbon\Carbon::parse($job->application_deadline)->isPast())
                                                        <br><span class="text-danger">(Berakhir)</span>
                                                    @endif
                                                </small>
                                            @else
                                                <small class="text-muted">-</small>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($job->created_at)->format('d/m/Y H:i') }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('jobs.show', $job->id) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('company.jobs.edit', $job->id) }}" 
                                                   class="btn btn-sm btn-outline-warning" 
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($job->status === 'active')
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-secondary" 
                                                            title="Tutup Lowongan"
                                                            onclick="confirmClose({{ $job->id }}, '{{ $job->title }}')">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @endif
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger" 
                                                        title="Hapus"
                                                        onclick="confirmDelete({{ $job->id }}, '{{ $job->title }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $jobs->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada lowongan</h5>
                            <p class="text-muted">Mulai dengan membuat lowongan kerja pertama Anda</p>
                            <a href="{{ route('company.jobs.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Buat Lowongan Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus lowongan "<strong id="jobTitle"></strong>"?</p>
                <p class="text-danger"><small>Perubahan ini tidak dapat dibatalkan.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Close Confirmation Modal -->
<div class="modal fade" id="closeModal" tabindex="-1" aria-labelledby="closeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="closeModalLabel">Konfirmasi Tutup Lowongan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menutup lowongan "<strong id="closeJobTitle"></strong>"?</p>
                <p class="text-warning"><small>Lowongan yang ditutup tidak akan muncul di pencarian kandidat.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="closeForm" method="POST" style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-warning">Tutup Lowongan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(jobId, jobTitle) {
    document.getElementById('jobTitle').textContent = jobTitle;
    document.getElementById('deleteForm').action = `/company/jobs/${jobId}`;
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

function confirmClose(jobId, jobTitle) {
    document.getElementById('closeJobTitle').textContent = jobTitle;
    document.getElementById('closeForm').action = `/company/jobs/${jobId}/close`;
    
    const closeModal = new bootstrap.Modal(document.getElementById('closeModal'));
    closeModal.show();
}

// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});
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

.card {
    border: 0;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.bg-light {
    background-color: #f8f9fa !important;
}

.bg-light:hover {
    background-color: #e9ecef !important;
    transition: background-color 0.2s ease-in-out;
}

.text-decoration-none:hover {
    text-decoration: none !important;
}
</style>
@endsection 