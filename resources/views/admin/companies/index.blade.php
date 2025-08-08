@extends('layouts.app')

@section('title', 'Kelola Perusahaan - Admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-building text-primary me-2"></i>
                        Kelola Perusahaan
                    </h1>
                    <p class="text-muted mb-0">Manajemen data perusahaan mitra BKK</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-success" onclick="exportData()">
                        <i class="fas fa-download me-1"></i>
                        Export
                    </button>
                    <a href="{{ route('admin.companies.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        Tambah Perusahaan
                    </a>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card border-0 shadow-sm bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white-50 mb-1">Total Perusahaan</h6>
                                    <h3 class="mb-0">{{ $companies->total() }}</h3>
                                </div>
                                <i class="fas fa-building fa-2x text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card border-0 shadow-sm bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white-50 mb-1">Aktif</h6>
                                    <h3 class="mb-0">{{ $companies->where('status', 'active')->count() }}</h3>
                                </div>
                                <i class="fas fa-check-circle fa-2x text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card border-0 shadow-sm bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white-50 mb-1">Pending</h6>
                                    <h3 class="mb-0">{{ $companies->where('status', 'pending')->count() }}</h3>
                                </div>
                                <i class="fas fa-clock fa-2x text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card border-0 shadow-sm bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white-50 mb-1">Bulan Ini</h6>
                                    <h3 class="mb-0">{{ $companies->where('created_at', '>=', now()->startOfMonth())->count() }}</h3>
                                </div>
                                <i class="fas fa-calendar fa-2x text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter and Search -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.companies.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Cari Perusahaan</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" placeholder="Nama perusahaan atau email...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Semua Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="industry" class="form-label">Industri</label>
                            <select class="form-select" id="industry" name="industry">
                                <option value="">Semua Industri</option>
                                <option value="teknologi" {{ request('industry') == 'teknologi' ? 'selected' : '' }}>Teknologi</option>
                                <option value="manufaktur" {{ request('industry') == 'manufaktur' ? 'selected' : '' }}>Manufaktur</option>
                                <option value="perdagangan" {{ request('industry') == 'perdagangan' ? 'selected' : '' }}>Perdagangan</option>
                                <option value="jasa" {{ request('industry') == 'jasa' ? 'selected' : '' }}>Jasa</option>
                                <option value="pendidikan" {{ request('industry') == 'pendidikan' ? 'selected' : '' }}>Pendidikan</option>
                                <option value="kesehatan" {{ request('industry') == 'kesehatan' ? 'selected' : '' }}>Kesehatan</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="sort" class="form-label">Urutkan</label>
                            <select class="form-select" id="sort" name="sort">
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama A-Z</option>
                                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Nama Z-A</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-1">
                                <button type="submit" class="btn btn-primary flex-fill">
                                    <i class="fas fa-filter me-1"></i>Filter
                                </button>
                                <a href="{{ route('admin.companies.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-redo"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Companies Table -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            Daftar Perusahaan
                        </h5>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                                <i class="fas fa-check-square me-1"></i>Pilih Semua
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="bulkAction('delete')" disabled id="bulkDeleteBtn">
                                <i class="fas fa-trash me-1"></i>Hapus Terpilih
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($companies->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40">
                                            <input type="checkbox" class="form-check-input" id="selectAllCheckbox">
                                        </th>
                                        <th>Perusahaan</th>
                                        <th>Kontak</th>
                                        <th>Industri</th>
                                        <th>Status</th>
                                        <th>Lowongan</th>
                                        <th>Bergabung</th>
                                        <th width="120">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($companies as $company)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="form-check-input row-checkbox" value="{{ $company->id }}">
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3">
                                                        {{ strtoupper(substr($company->company_name, 0, 2)) }}
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $company->company_name }}</h6>
                                                        <small class="text-muted">{{ Str::limit($company->description ?? 'Tidak ada deskripsi', 50) }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <i class="fas fa-envelope text-muted me-1"></i>{{ $company->email }}
                                                </div>
                                                @if($company->phone)
                                                    <div>
                                                        <i class="fas fa-phone text-muted me-1"></i>{{ $company->phone }}
                                                    </div>
                                                @endif
                                                @if($company->website)
                                                    <div>
                                                        <i class="fas fa-globe text-muted me-1"></i>
                                                        <a href="{{ $company->website }}" target="_blank" class="text-decoration-none">
                                                            {{ parse_url($company->website, PHP_URL_HOST) }}
                                                        </a>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                @if($company->industry)
                                                    <span class="badge bg-light text-dark">{{ ucfirst($company->industry) }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $statusConfig = [
                                                        'active' => ['class' => 'success', 'text' => 'Aktif'],
                                                        'pending' => ['class' => 'warning', 'text' => 'Pending'],
                                                        'inactive' => ['class' => 'danger', 'text' => 'Tidak Aktif'],
                                                    ];
                                                    $status = $statusConfig[$company->status] ?? ['class' => 'secondary', 'text' => ucfirst($company->status)];
                                                @endphp
                                                <span class="badge bg-{{ $status['class'] }}">{{ $status['text'] }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $company->jobs_count ?? 0 }} Lowongan</span>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $company->created_at->format('d M Y') }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                                            onclick="viewCompany({{ $company->id }})" title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <a href="{{ route('admin.companies.edit', $company) }}" 
                                                       class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                                            onclick="deleteCompany({{ $company->id }}, '{{ $company->company_name }}')" title="Hapus">
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
                        <div class="d-flex justify-content-center mt-4 px-3 pb-3">
                            {{ $companies->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-building fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum Ada Perusahaan</h5>
                            <p class="text-muted">Data perusahaan akan muncul di sini setelah perusahaan mendaftar.</p>
                            <a href="{{ route('admin.companies.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Tambah Perusahaan
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Company Detail Modal -->
<div class="modal fade" id="companyDetailModal" tabindex="-1" aria-labelledby="companyDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="companyDetailModalLabel">
                    <i class="fas fa-building me-2"></i>
                    Detail Perusahaan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="companyDetailContent">
                <!-- Company details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                    Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus perusahaan "<span id="companyName"></span>"?</p>
                <p class="text-danger small">
                    <i class="fas fa-warning me-1"></i>
                    Tindakan ini akan menghapus semua data terkait termasuk lowongan kerja dan tidak dapat dibatalkan.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>
                        Hapus Perusahaan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.avatar-sm {
    width: 40px;
    height: 40px;
    font-size: 14px;
    font-weight: bold;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

.badge {
    font-size: 0.75em;
}

.btn-group .btn {
    margin-right: 2px;
}
</style>
@endpush

@push('scripts')
<script>
// Company detail view
function viewCompany(companyId) {
    fetch(`/admin/companies/${companyId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const company = data.data;
                const modalContent = document.getElementById('companyDetailContent');
                
                modalContent.innerHTML = `
                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="text-primary mb-3">Informasi Perusahaan</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Nama Perusahaan</strong></td>
                                    <td>: ${company.company_name}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email</strong></td>
                                    <td>: ${company.email}</td>
                                </tr>
                                <tr>
                                    <td><strong>Telepon</strong></td>
                                    <td>: ${company.phone || '-'}</td>
                                </tr>
                                <tr>
                                    <td><strong>Website</strong></td>
                                    <td>: ${company.website ? `<a href="${company.website}" target="_blank">${company.website}</a>` : '-'}</td>
                                </tr>
                                <tr>
                                    <td><strong>Industri</strong></td>
                                    <td>: ${company.industry || '-'}</td>
                                </tr>
                                <tr>
                                    <td><strong>Alamat</strong></td>
                                    <td>: ${company.address || '-'}</td>
                                </tr>
                                <tr>
                                    <td><strong>Deskripsi</strong></td>
                                    <td>: ${company.description || '-'}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-primary mb-3">Statistik</h6>
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h4 class="text-primary mb-1">${company.jobs_count || 0}</h4>
                                    <small class="text-muted">Total Lowongan</small>
                                </div>
                            </div>
                            <div class="card bg-light mt-2">
                                <div class="card-body text-center">
                                    <h4 class="text-success mb-1">${company.applications_count || 0}</h4>
                                    <small class="text-muted">Total Lamaran</small>
                                </div>
                            </div>
                            <div class="mt-3">
                                <small class="text-muted">Bergabung: ${new Date(company.created_at).toLocaleDateString('id-ID')}</small>
                            </div>
                        </div>
                    </div>
                `;
                
                const modal = new bootstrap.Modal(document.getElementById('companyDetailModal'));
                modal.show();
            } else {
                alert('Gagal memuat detail perusahaan');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memuat data');
        });
}

// Delete company
function deleteCompany(companyId, companyName) {
    document.getElementById('companyName').textContent = companyName;
    document.getElementById('deleteForm').action = `/admin/companies/${companyId}`;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Bulk actions
function selectAll() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    
    rowCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    
    updateBulkActionButtons();
}

function updateBulkActionButtons() {
    const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    
    if (selectedCheckboxes.length > 0) {
        bulkDeleteBtn.disabled = false;
        bulkDeleteBtn.textContent = `Hapus ${selectedCheckboxes.length} item`;
    } else {
        bulkDeleteBtn.disabled = true;
        bulkDeleteBtn.innerHTML = '<i class="fas fa-trash me-1"></i>Hapus Terpilih';
    }
}

function bulkAction(action) {
    const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
    const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);
    
    if (selectedIds.length === 0) {
        alert('Pilih minimal satu perusahaan untuk dihapus');
        return;
    }
    
    if (action === 'delete') {
        if (confirm(`Apakah Anda yakin ingin menghapus ${selectedIds.length} perusahaan?`)) {
            // Implementation for bulk delete
        }
    }
}

function exportData() {
    window.location.href = '/admin/companies/export?' + new URLSearchParams(window.location.search);
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Update bulk action buttons when checkboxes change
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('row-checkbox')) {
            updateBulkActionButtons();
        }
    });
    
    // Select all functionality
    document.getElementById('selectAllCheckbox').addEventListener('change', selectAll);
});
</script>
@endpush
