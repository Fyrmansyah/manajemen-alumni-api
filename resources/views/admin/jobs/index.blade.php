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
                                <option value="">Semua Perusahaan</option>
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
                            <button class="btn btn-outline-primary btn-sm" onclick="selectAll()">
                                <i class="fas fa-check-square me-1"></i>Pilih Semua
                            </button>
                            <button class="btn btn-outline-secondary btn-sm" onclick="deselectAll()">
                                <i class="fas fa-square me-1"></i>Batal Pilih
                            </button>
                        </div>
                        <div class="bulk-actions" style="display: none;">
                            @if(!isset($view) || $view == 'active')
                                <button class="btn btn-warning btn-sm" onclick="bulkArchive()">
                                    <i class="fas fa-archive me-1"></i>Arsipkan Terpilih
                                </button>
                            @elseif($view == 'archived')
                                <button class="btn btn-success btn-sm" onclick="bulkReactivate()">
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
                                                <span class="badge bg-{{ $job->status == 'active' ? 'success' : ($job->status == 'closed' ? 'danger' : 'warning') }}">
                                                    {{ ucfirst($job->status) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $job->applications_count ?? 0 }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.jobs.show', $job) }}" 
                                                   class="btn btn-outline-info btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.jobs.edit', $job) }}" 
                                                   class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                @if($job->isArchived())
                                                    <button class="btn btn-outline-success btn-sm" 
                                                            onclick="reactivateJob({{ $job->id }})" 
                                                            title="Aktifkan Kembali">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                @else
                                                    <button class="btn btn-outline-warning btn-sm" 
                                                            onclick="archiveJob({{ $job->id }})" 
                                                            title="Arsipkan">
                                                        <i class="fas fa-archive"></i>
                                                    </button>
                                                @endif
                                                
                                                <button class="btn btn-outline-danger btn-sm" 
                                                        onclick="deleteJob({{ $job->id }})" 
                                                        title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
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
@endsection

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
    form.action = `{{ route('admin.jobs.index') }}/${jobId}/archive`;
    
    const modal = new bootstrap.Modal(document.getElementById('archiveModal'));
    modal.show();
}

function reactivateJob(jobId) {
    if (confirm('Apakah Anda yakin ingin mengaktifkan kembali lowongan ini?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('admin.jobs.index') }}/${jobId}/reactivate`;
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.innerHTML = `
            <input type="hidden" name="_token" value="${csrfToken}">
            <input type="hidden" name="_method" value="PATCH">
        `;
        
        document.body.appendChild(form);
        form.submit();
    }
}

function deleteJob(jobId) {
    if (confirm('Apakah Anda yakin ingin menghapus lowongan ini? Tindakan ini tidak dapat dibatalkan.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('admin.jobs.index') }}/${jobId}`;
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.innerHTML = `
            <input type="hidden" name="_token" value="${csrfToken}">
            <input type="hidden" name="_method" value="DELETE">
        `;
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Bulk operations
function bulkArchive() {
    const selectedIds = Array.from(document.querySelectorAll('.row-checkbox:checked'))
                             .map(cb => cb.value);
    
    if (selectedIds.length === 0) {
        alert('Pilih minimal satu lowongan untuk diarsipkan');
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

function bulkReactivate() {
    const selectedIds = Array.from(document.querySelectorAll('.row-checkbox:checked'))
                             .map(cb => cb.value);
    
    if (selectedIds.length === 0) {
        alert('Pilih minimal satu lowongan untuk diaktifkan');
        return;
    }
    
    if (confirm(`Apakah Anda yakin ingin mengaktifkan kembali ${selectedIds.length} lowongan?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.jobs.bulk-reactivate") }}';
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.innerHTML = `
            <input type="hidden" name="_token" value="${csrfToken}">
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

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
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
});
</script>
@endpush
