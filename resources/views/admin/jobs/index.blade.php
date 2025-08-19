@extends('layouts.app')

@section('title', 'Kelola Lowongan Kerja - Admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-briefcase text-primary me-2"></i>
                        Kelola Lowongan Kerja
                    </h1>
                    <p class="text-muted mb-0">Manajemen lowongan kerja dari semua perusahaan</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-success" onclick="exportData()">
                        <i class="fas fa-download me-1"></i>
                        Export
                    </button>
                    <a href="{{ route('admin.jobs.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        Tambah Lowongan
                    </a>
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

            <!-- Expired Jobs Notification -->
            @php
                $expiredJobsCount = App\Models\Job::expiredButNotArchived()->count();
            @endphp
            @if($expiredJobsCount > 0)
                <div id="expired-notification" class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-clock me-2"></i>
                    <strong>{{ $expiredJobsCount }} lowongan telah kadaluarsa</strong> dan akan diarsip otomatis saat halaman dimuat ulang.
                    <small class="d-block mt-1">
                        <i class="fas fa-info-circle me-1"></i>
                        Sistem akan memindahkan lowongan kadaluarsa ke arsip secara otomatis setiap 30 detik.
                    </small>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $counts['active'] ?? 0 }}</h4>
                                    <span class="small">Lowongan Aktif</span>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-briefcase fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-secondary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $counts['archived'] ?? 0 }}</h4>
                                    <span class="small">Lowongan Diarsip</span>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-archive fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $counts['all'] ?? 0 }}</h4>
                                    <span class="small">Total Lowongan</span>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-list fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-warning text-dark">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ App\Models\Job::expiredButNotArchived()->count() }}</h4>
                                    <span class="small">Perlu Diarsip</span>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter & Search -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Search</label>
                            <input type="text" class="form-control" name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Cari judul, deskripsi, atau perusahaan...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="">Semua Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Ditutup</option>
                                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Kadaluarsa</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Perusahaan</label>
                            <select class="form-select" name="company_id">
                                                        <option value="">All Companies</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                {{ $company->company_name }}
                            </option>
                        @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Sort</label>
                            <select class="form-select" name="sort">
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                                <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>Judul A-Z</option>
                                <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>Judul Z-A</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>Filter
                                </button>
                                <a href="{{ route('admin.jobs.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- View Tabs -->
            <div class="card shadow-sm">
                <div class="card-header border-0">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ (!isset($view) || $view == 'active') ? 'active' : '' }}" 
                               href="{{ route('admin.jobs.index', ['view' => 'active'] + request()->except('view')) }}">
                                <i class="fas fa-briefcase me-1"></i>
                                Aktif <span class="badge bg-primary ms-1">{{ $counts['active'] ?? 0 }}</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $view == 'archived' ? 'active' : '' }}" 
                               href="{{ route('admin.jobs.index', ['view' => 'archived'] + request()->except('view')) }}">
                                <i class="fas fa-archive me-1"></i>
                                Diarsip <span class="badge bg-secondary ms-1">{{ $counts['archived'] ?? 0 }}</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $view == 'all' ? 'active' : '' }}" 
                               href="{{ route('admin.jobs.index', ['view' => 'all'] + request()->except('view')) }}">
                                <i class="fas fa-list me-1"></i>
                                Semua <span class="badge bg-info ms-1">{{ $counts['all'] ?? 0 }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="card-body">
                    <!-- Bulk Actions -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <button class="btn btn-outline-primary btn-sm" 
                                    onclick="selectAll()"
                                    title="Pilih semua lowongan yang terlihat">
                                <i class="fas fa-check-square me-1"></i>Pilih Semua
                            </button>
                            <button class="btn btn-outline-secondary btn-sm" 
                                    onclick="deselectAll()"
                                    title="Batalkan pilihan semua lowongan">
                                <i class="fas fa-square me-1"></i>Batal Pilih
                            </button>
                        </div>
                        <div class="bulk-actions" style="display: none;">
                            @if(!isset($view) || $view == 'active')
                                <button class="btn btn-warning btn-sm" 
                                        onclick="bulkArchive()"
                                        title="Arsipkan semua lowongan yang dipilih">
                                    <i class="fas fa-archive me-1"></i>Arsipkan Terpilih
                                </button>
                            @elseif($view == 'archived')
                                <button class="btn btn-success btn-sm" 
                                        onclick="bulkReactivate()"
                                        title="Aktifkan kembali semua lowongan yang dipilih">
                                    <i class="fas fa-undo me-1"></i>Aktifkan Terpilih
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Jobs Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" class="form-check-input" id="selectAllCheckbox">
                                    </th>
                                    <th>Lowongan</th>
                                    <th>Perusahaan</th>
                                    <th>Lokasi</th>
                                    <th>Deadline</th>
                                    <th>Status</th>
                                    <th>Lamar</th>
                                    <th width="200">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($jobs as $job)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="form-check-input row-checkbox" 
                                                   value="{{ $job->id }}">
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $job->title }}</div>
                                            <small class="text-muted">{{ Str::limit($job->description, 60) }}</small>
                                            @if($job->isArchived())
                                                <br><small class="text-warning">
                                                    <i class="fas fa-archive me-1"></i>
                                                    Diarsip: {{ $job->archived_at->format('d M Y') }}
                                                    @if($job->archive_reason)
                                                        ({{ $job->archive_reason }})
                                                    @endif
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $job->company->company_name }}</div>
                                            <small class="text-muted">{{ $job->company->industry ?? 'N/A' }}</small>
                                        </td>
                                        <td>{{ $job->location }}</td>
                                        <td>
                                            @if($job->application_deadline)
                                                <div class="{{ $job->application_deadline->isPast() ? 'text-danger' : 'text-success' }}">
                                                    {{ $job->application_deadline->format('d M Y') }}
                                                </div>
                                                @if($job->application_deadline->isPast())
                                                    <small class="text-muted">
                                                        {{ $job->application_deadline->diffForHumans() }}
                                                    </small>
                                                @endif
                                            @else
                                                <span class="text-muted">Tidak ditentukan</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($job->isArchived())
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-archive me-1"></i>Diarsip
                                                </span>
                                            @else
                                                @php
                                                    $realTimeStatus = $job->real_time_status;
                                                    $statusDisplay = $job->status_display;
                                                    $statusClass = $job->status_css_class;
                                                @endphp
                                                <span class="badge bg-{{ $statusClass }}">
                                                    @if($realTimeStatus === 'expired')
                                                        <i class="fas fa-clock me-1"></i>
                                                    @elseif($realTimeStatus === 'active')
                                                        <i class="fas fa-check-circle me-1"></i>
                                                    @elseif($realTimeStatus === 'draft')
                                                        <i class="fas fa-edit me-1"></i>
                                                    @elseif($realTimeStatus === 'closed')
                                                        <i class="fas fa-times-circle me-1"></i>
                                                    @endif
                                                    {{ $statusDisplay }}
                                                </span>
                                                @if($realTimeStatus === 'expired' && !$job->isArchived())
                                                    <small class="text-danger d-block mt-1">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                                        Akan diarsip otomatis
                                                    </small>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $job->applications_count ?? 0 }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group" aria-label="Job Actions">
                                                <a href="{{ route('admin.jobs.show', $job) }}" 
                                                   class="btn btn-outline-info btn-sm" 
                                                   title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                @if(!$job->isArchived())
                                                    <a href="{{ route('admin.jobs.edit', $job) }}" 
                                                       class="btn btn-outline-primary btn-sm"
                                                       title="Edit Lowongan">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                                
                                                @if($job->isArchived())
                                                    <form action="{{ route('admin.jobs.reactivate', $job) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="extend_if_expired" value="1">
                                                        <button type="submit" class="btn btn-outline-success btn-sm" 
                                                                onclick="return confirm('Aktifkan kembali lowongan ini? Jika deadline sudah lewat, sistem akan otomatis memperpanjang 30 hari.')"
                                                                title="Aktifkan Kembali">
                                                            <i class="fas fa-undo"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <button class="btn btn-outline-warning btn-sm" 
                                                            onclick="archiveSingleJob({{ $job->id }})"
                                                            title="Arsipkan Lowongan">
                                                        <i class="fas fa-archive me-1"></i>
                                                    </button>
                                                @endif
                                                
                                                @if(!$job->isArchived())
                                                    <form action="{{ route('admin.jobs.destroy', $job) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger btn-sm" 
                                                                onclick="return confirm('Yakin ingin menghapus lowongan ini? Tindakan ini tidak dapat dibatalkan!')"
                                                                title="Hapus Lowongan">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                                <p>Tidak ada lowongan kerja ditemukan</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($jobs->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted">
                                Menampilkan {{ $jobs->firstItem() ?? 0 }} - {{ $jobs->lastItem() ?? 0 }} 
                                dari {{ $jobs->total() }} lowongan
                            </div>
                            {{ $jobs->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Archive Modal -->
<div class="modal fade" id="archiveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Arsipkan Lowongan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="archiveForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Alasan Pengarsipan</label>
                        <textarea class="form-control" name="reason" rows="3" 
                                  placeholder="Masukkan alasan pengarsipan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Arsipkan</button>
                </div>
            </form>
        </div>
    </div>
    </div>
    
    <!-- Toast Notifications -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 11;">
        @if(session('success'))
            <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header bg-success text-white">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong class="me-auto">Berhasil</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    {{ session('success') }}
                </div>
            </div>
        @endif
        
        @if(session('error'))
            <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header bg-danger text-white">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong class="me-auto">Error</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    {{ session('error') }}
                </div>
            </div>
        @endif
        
        @if(session('warning'))
            <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header bg-warning text-dark">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong class="me-auto">Peringatan</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    {{ session('warning') }}
                </div>
            </div>
        @endif
        
        @if(session('info'))
            <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header bg-info text-white">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong class="me-auto">Informasi</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    {{ session('info') }}
                </div>
            </div>
        @endif
    </div>
    
@endsection

@push('styles')
<style>
/* Professional button styling */
.btn-group .btn {
    border-radius: 0;
    border-right-width: 0;
    transition: all 0.2s ease-in-out;
}

.btn-group .btn:first-child {
    border-top-left-radius: 0.375rem;
    border-bottom-left-radius: 0.375rem;
}

.btn-group .btn:last-child {
    border-top-right-radius: 0.375rem;
    border-bottom-right-radius: 0.375rem;
    border-right-width: 1px;
}

.btn-group .btn:hover {
    z-index: 2;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.btn-sm {
    padding: 0.4rem 0.8rem;
    font-size: 0.875rem;
    min-width: 40px;
}

/* Table improvements */
.table-hover tbody tr:hover {
    background-color: rgba(0,123,255,0.05);
}

/* Card styling */
.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: box-shadow 0.15s ease-in-out;
}

.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

/* Badge improvements */
.badge {
    font-size: 0.75em;
    padding: 0.5em 0.75em;
}

/* Statistics cards hover effect */
.col-md-3:hover .card {
    transform: translateY(-2px);
    transition: transform 0.2s ease-in-out;
}

/* Button color improvements */
.btn-outline-info:hover {
    background-color: #17a2b8;
    border-color: #17a2b8;
}

.btn-outline-primary:hover {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.btn-outline-success:hover {
    background-color: #198754;
    border-color: #198754;
}

.btn-outline-warning:hover {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #000;
}

.btn-outline-danger:hover {
    background-color: #dc3545;
    border-color: #dc3545;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .btn-group {
        flex-direction: column;
        width: 100%;
    }
    
    .btn-group .btn {
        border-radius: 0.375rem !important;
        border-right-width: 1px;
        margin-bottom: 0.25rem;
    }
    
    .btn-group .btn:last-child {
        margin-bottom: 0;
    }
}

/* Loading animation for buttons */
.btn:active {
    transform: scale(0.98);
}

/* Improved spacing */
.table td {
    vertical-align: middle;
    padding: 0.75rem;
}

.table th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    color: #495057;
}
</style>
@endpush

@push('scripts')
<script>
// Select all functionality
function selectAll() {
    document.querySelectorAll('.row-checkbox').forEach(checkbox => {
        checkbox.checked = true;
    });
    document.getElementById('selectAllCheckbox').checked = true;
    updateBulkActionButtons();
}

function deselectAll() {
    document.querySelectorAll('.row-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    document.getElementById('selectAllCheckbox').checked = false;
    updateBulkActionButtons();
}

function updateBulkActionButtons() {
    const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
    const bulkActions = document.querySelector('.bulk-actions');
    
    if (selectedCheckboxes.length > 0) {
        bulkActions.style.display = 'block';
    } else {
        bulkActions.style.display = 'none';
    }
}

// Archive functions
function archiveJob(jobId) {
    const form = document.getElementById('archiveForm');
    form.action = `{{ url('/admin/jobs') }}/${jobId}/archive`;
    
    const modal = new bootstrap.Modal(document.getElementById('archiveModal'));
    modal.show();
}

// Add loading states to buttons
function addLoadingState(button) {
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Memproses...';
    
    // Restore button after 3 seconds if still disabled
    setTimeout(() => {
        if (button.disabled) {
            button.disabled = false;
            button.innerHTML = originalText;
        }
    }, 3000);
}

// Enhanced confirmation dialogs
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

// Add tooltip initialization
function initializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

// Toast notification functions
function showToast(message, type = 'success') {
    const toastContainer = document.querySelector('.toast-container');
    const toastId = 'toast-' + Date.now();
    
    const iconClass = {
        'success': 'fa-check-circle',
        'error': 'fa-exclamation-circle', 
        'warning': 'fa-exclamation-triangle',
        'info': 'fa-info-circle'
    }[type] || 'fa-info-circle';
    
    const bgClass = {
        'success': 'bg-success text-white',
        'error': 'bg-danger text-white',
        'warning': 'bg-warning text-dark', 
        'info': 'bg-info text-white'
    }[type] || 'bg-info text-white';
    
    const closeClass = type === 'warning' ? 'btn-close' : 'btn-close btn-close-white';
    
    const toastHtml = `
        <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header ${bgClass}">
                <i class="fas ${iconClass} me-2"></i>
                <strong class="me-auto">${type.charAt(0).toUpperCase() + type.slice(1)}</strong>
                <button type="button" class="${closeClass}" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, {
        autohide: true,
        delay: 5000
    });
    
    toast.show();
    
    // Remove toast element after it's hidden
    toastElement.addEventListener('hidden.bs.toast', function () {
        this.remove();
    });
}

// Enhanced status update notifications
function notifyStatusChange(jobTitle, oldStatus, newStatus) {
    const message = `Lowongan "${jobTitle}" berubah dari ${oldStatus} menjadi ${newStatus}`;
    showToast(message, 'info');
}

// Bulk operations
function bulkArchive() {
    const selectedIds = Array.from(document.querySelectorAll('.row-checkbox:checked'))
                             .map(cb => cb.value);
    
    if (selectedIds.length === 0) {
        alert('Pilih minimal satu lowongan untuk diarsipkan.');
        return;
    }
    
    if (!confirm(`Yakin ingin mengarsipkan ${selectedIds.length} lowongan yang dipilih?`)) {
        return;
    }
    
    const reason = prompt('Masukkan alasan pengarsipan:');
    if (reason === null) return; // User cancelled
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.jobs.bulk-archive") }}';
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    form.innerHTML = `
        <input type="hidden" name="_token" value="${csrfToken}">
        <input type="hidden" name="reason" value="${reason}">
        ${selectedIds.map(id => `<input type="hidden" name="job_ids[]" value="${id}">`).join('')}
    `;
    
    document.body.appendChild(form);
    form.submit();
}

function archiveSingleJob(jobId) {
    if (!confirm('Yakin ingin mengarsipkan lowongan ini?')) {
        return;
    }
    
    const reason = prompt('Masukkan alasan pengarsipan:');
    if (reason === null) return; // User cancelled
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `{{ url('/admin/jobs') }}/${jobId}/archive`;
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    form.innerHTML = `
        <input type="hidden" name="_token" value="${csrfToken}">
        <input type="hidden" name="_method" value="PATCH">
        <input type="hidden" name="reason" value="${reason}">
    `;
    
    document.body.appendChild(form);
    form.submit();
}

function bulkReactivate() {
    const selectedIds = Array.from(document.querySelectorAll('.row-checkbox:checked'))
                             .map(cb => cb.value);
    
    if (selectedIds.length === 0) {
        alert('Pilih minimal satu lowongan untuk diaktifkan');
        return;
    }
    
    if (confirm(`Apakah Anda yakin ingin mengaktifkan kembali ${selectedIds.length} lowongan?\n\nCatatan: Jika deadline lowongan sudah lewat, sistem akan otomatis memperpanjang selama 30 hari.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.jobs.bulk-reactivate") }}';
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.innerHTML = `
            <input type="hidden" name="_token" value="${csrfToken}">
            <input type="hidden" name="extend_if_expired" value="1">
            ${selectedIds.map(id => `<input type="hidden" name="job_ids[]" value="${id}">`).join('')}
        `;
        
        document.body.appendChild(form);
        form.submit();
    }
}

function exportData() {
    const params = new URLSearchParams(window.location.search);
    window.location.href = '{{ route("admin.jobs.index") }}/export?' + params.toString();
}

// Auto-refresh untuk memperbarui status expired jobs
let autoRefreshInterval;

function startAutoRefresh() {
    // Refresh setiap 30 detik untuk memperbarui status expired
    autoRefreshInterval = setInterval(() => {
        const currentUrl = new URL(window.location.href);
        const params = currentUrl.searchParams;
        
        // Tambahkan timestamp untuk mencegah caching
        params.set('_t', Date.now());
        
        // Refresh halaman dengan parameter yang sama
        window.location.href = currentUrl.pathname + '?' + params.toString();
    }, 30000); // 30 detik
}

function stopAutoRefresh() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
}

// Notifikasi real-time untuk expired jobs
function checkForExpiredJobs() {
    // Cek apakah ada job yang status expired tapi belum diarsip
    const allBadges = document.querySelectorAll('span.badge.bg-danger');
    const expiredJobs = Array.from(allBadges).filter(badge => 
        badge.textContent.trim().includes('Kadaluarsa')
    );
    
    if (expiredJobs.length > 0) {
        // Tampilkan notifikasi bahwa ada job expired
        const notification = document.getElementById('expired-notification');
        if (notification) {
            notification.style.display = 'block';
            // Update count if there's a number in the notification text
            const strongElement = notification.querySelector('strong');
            if (strongElement) {
                strongElement.textContent = `${expiredJobs.length} lowongan telah kadaluarsa`;
            }
        }
    }
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    initializeTooltips();
    
    // Initialize existing toasts and auto-hide them
    document.querySelectorAll('.toast.show').forEach(function(toastElement) {
        const toast = new bootstrap.Toast(toastElement, {
            autohide: true,
            delay: 5000
        });
        
        // Auto-hide after 5 seconds if not manually closed
        setTimeout(() => {
            if (toastElement.classList.contains('show')) {
                toast.hide();
            }
        }, 5000);
        
        // Remove toast element after it's hidden
        toastElement.addEventListener('hidden.bs.toast', function () {
            this.remove();
        });
    });
    
    // Add loading states to action forms
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                addLoadingState(submitBtn);
            }
        });
    });
    
    // Enhanced button interactions with loading states
    document.querySelectorAll('.btn').forEach(button => {
        button.addEventListener('click', function() {
            // Hanya terapkan loading state pada tombol tipe "button" (bukan submit)
            // Untuk tombol submit, loading state akan ditangani oleh event submit form
            if (!this.disabled && this.type === 'button' && !this.getAttribute('data-bs-toggle')) {
                addLoadingState(this);
            }
        });
    });
    
    // Update bulk action buttons when checkboxes change
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('row-checkbox')) {
            updateBulkActionButtons();
        }
    });
    
    // Handle select all checkbox
    document.getElementById('selectAllCheckbox').addEventListener('change', function(e) {
        if (e.target.checked) {
            selectAll();
        } else {
            deselectAll();
        }
    });
    
    // Mulai auto-refresh untuk memonitor expired jobs
    startAutoRefresh();
    
    // Check for expired jobs on load
    checkForExpiredJobs();
    
    // Stop auto-refresh when user leaves page
    window.addEventListener('beforeunload', function() {
        stopAutoRefresh();
    });
    
    // Add smooth scrolling for better UX
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl + A to select all
        if (e.ctrlKey && e.key === 'a' && e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
            e.preventDefault();
            selectAll();
        }
        
        // Escape to deselect all
        if (e.key === 'Escape') {
            deselectAll();
        }
    });
});
</script>
@endpush
