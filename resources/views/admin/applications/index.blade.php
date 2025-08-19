@extends('layouts.app')

@section('title', 'Kelola Aplikasi - Admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-file-alt text-primary me-2"></i>
                        Kelola Aplikasi Lamaran
                    </h1>
                    <p class="text-muted mb-0">Monitor dan kelola semua aplikasi lamaran kerja</p>
                </div>
            </div>

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

            <!-- Filter Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 pb-0">
                    <h6 class="mb-0">
                        <i class="fas fa-filter me-2"></i>Filter & Pencarian
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.applications.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Cari Aplikasi</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Nama alumni atau perusahaan...">
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Semua Status</option>
                                <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                                <option value="reviewed" {{ request('status') == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                                <option value="interview" {{ request('status') == 'interview' ? 'selected' : '' }}>Interview</option>
                                <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="sort" class="form-label">Urutkan</label>
                            <select class="form-select" id="sort" name="sort">
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Applications List -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-list me-2"></i>Daftar Aplikasi Lamaran
                    </h6>
                    <span class="badge bg-secondary">{{ $applications->total() }} Total</span>
                </div>
                <div class="card-body p-0">
                    @if($applications->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Alumni</th>
                                        <th>Posisi</th>
                                        <th>Perusahaan</th>
                                        <th>Tanggal Apply</th>
                                        <th>Status</th>
                                        <th width="120">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($applications as $application)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm rounded-circle bg-primary text-white me-3 d-flex align-items-center justify-content-center">
                                                        {{ strtoupper(substr($application->alumni->nama, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $application->alumni->nama }}</h6>
                                                        <small class="text-muted">{{ $application->alumni->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <strong>{{ $application->job->title }}</strong>
                                            </td>
                                            <td>
                                                <div>
                                                    <span class="fw-medium">{{ $application->job->company->name }}</span>
                                                    <br>
                                                    <small class="text-muted">{{ $application->job->location }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $application->applied_at->format('d/m/Y H:i') }}</span>
                                            </td>
                                            <td>
                                                @if($application->status == 'submitted')
                                                    <span class="badge bg-info">Submitted</span>
                                                @elseif($application->status == 'reviewed')
                                                    <span class="badge bg-warning">Reviewed</span>
                                                @elseif($application->status == 'interview')
                                                    <span class="badge bg-primary">Interview</span>
                                                @elseif($application->status == 'accepted')
                                                    <span class="badge bg-success">Accepted</span>
                                                @elseif($application->status == 'rejected')
                                                    <span class="badge bg-danger">Rejected</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                            type="button" 
                                                            data-bs-toggle="dropdown">
                                                        Aksi
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('admin.applications.show', $application) }}">
                                                                <i class="fas fa-eye me-2"></i>Detail
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form action="{{ route('admin.applications.updateStatus', $application) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="hidden" name="status" value="reviewed">
                                                                <button type="submit" class="dropdown-item text-warning">
                                                                    <i class="fas fa-eye me-2"></i>Mark as Reviewed
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('admin.applications.updateStatus', $application) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="hidden" name="status" value="interview">
                                                                <button type="submit" class="dropdown-item text-primary">
                                                                    <i class="fas fa-calendar me-2"></i>Schedule Interview
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('admin.applications.updateStatus', $application) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="hidden" name="status" value="accepted">
                                                                <button type="submit" class="dropdown-item text-success">
                                                                    <i class="fas fa-check me-2"></i>Accept
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('admin.applications.updateStatus', $application) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <input type="hidden" name="status" value="rejected">
                                                                <button type="submit" class="dropdown-item text-danger">
                                                                    <i class="fas fa-times me-2"></i>Reject
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($applications->hasPages())
                            <div class="d-flex justify-content-center mt-4 px-3 pb-3">
                                {{ $applications->appends(request()->query())->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum Ada Aplikasi</h5>
                            <p class="text-muted">Aplikasi lamaran akan muncul di sini ketika alumni melamar kerja.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Status Aplikasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="statusForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status Baru</label>
                        <select class="form-select" name="status" id="modalStatus">
                            <option value="submitted">Submitted</option>
                            <option value="reviewed">Reviewed</option>
                            <option value="interview">Interview</option>
                            <option value="accepted">Accepted</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Catatan (Opsional)</label>
                        <textarea class="form-control" name="notes" id="notes" rows="3" 
                                  placeholder="Tambahkan catatan untuk perubahan status ini"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateStatus(applicationId, currentStatus) {
    const modal = new bootstrap.Modal(document.getElementById('statusModal'));
    const form = document.getElementById('statusForm');
    const statusSelect = document.getElementById('modalStatus');
    
    form.action = `/admin/applications/${applicationId}/status`;
    statusSelect.value = currentStatus;
    
    modal.show();
}

// Auto-submit form when filters change
document.querySelectorAll('#status, #sort').forEach(element => {
    element.addEventListener('change', function() {
        this.closest('form').submit();
    });
});
</script>
@endpush

@push('styles')
<style>
.avatar-sm {
    width: 40px;
    height: 40px;
    font-size: 16px;
    font-weight: 600;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-size: 0.75rem;
}
</style>
@endpush
