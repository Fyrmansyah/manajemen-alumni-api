@extends('layouts.app')

@section('title', 'Kelola Alumni - Admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-graduation-cap text-primary me-2"></i>
                        Kelola Alumni
                    </h1>
                    <p class="text-muted mb-0">Manajemen data alumni berdasarkan jurusan</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-success" onclick="exportData()">
                        <i class="fas fa-download me-1"></i>
                        Export
                    </button>
                    <button class="btn btn-outline-info" onclick="importData()">
                        <i class="fas fa-upload me-1"></i>
                        Import Alumni
                    </button>
                    <a href="{{ route('admin.alumni.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        Tambah Alumni
                    </a>
                </div>
            </div>

            <!-- Import Form -->

            <!-- Statistics by Department -->
            <div class="row mb-4">
                @foreach($jurusans as $jurusan)
                    <div class="col-md-2 mb-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center">
                                <div class="mb-2">
                                    <i class="fas fa-users fa-2x text-primary"></i>
                                </div>
                                <h5 class="mb-1">{{ $jurusan->alumni_count ?? 0 }}</h5>
                                <small class="text-muted">{{ $jurusan->nama_jurusan }}</small>
                                <div class="mt-2">
                                    <button class="btn btn-sm btn-outline-primary" 
                                            onclick="filterByJurusan('{{ $jurusan->id }}')">
                                        Lihat Detail
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                <div class="col-md-2 mb-3">
                    <div class="card border-0 shadow-sm bg-info text-white h-100">
                        <div class="card-body text-center">
                            <div class="mb-2">
                                <i class="fas fa-chart-bar fa-2x text-white-50"></i>
                            </div>
                            <h5 class="mb-1">{{ $totalAlumni }}</h5>
                            <small class="text-white-50">Total Alumni</small>
                            <div class="mt-2">
                                <button class="btn btn-sm btn-outline-light" onclick="showStatistics()">
                                    Statistik
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter and Search -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.alumni.index') }}" class="row g-3" id="filterForm">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Cari Alumni</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" placeholder="Nama, NISN, atau email...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="jurusan_id" class="form-label">Jurusan</label>
                            <select class="form-select" id="jurusan_id" name="jurusan_id">
                                <option value="">Semua Jurusan</option>
                                @foreach($jurusans as $jurusan)
                                    <option value="{{ $jurusan->id }}" {{ request('jurusan_id') == $jurusan->id ? 'selected' : '' }}>
                                        {{ $jurusan->nama_jurusan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="tahun_lulus" class="form-label">Tahun Lulus</label>
                            <select class="form-select" id="tahun_lulus" name="tahun_lulus">
                                <option value="">Semua Tahun</option>
                                @for($year = date('Y'); $year >= 2000; $year--)
                                    <option value="{{ $year }}" {{ request('tahun_lulus') == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="status_kerja" class="form-label">Status Kerja</label>
                            <select class="form-select" id="status_kerja" name="status_kerja">
                                <option value="">Semua Status</option>
                                <option value="bekerja" {{ request('status_kerja') == 'bekerja' ? 'selected' : '' }}>Bekerja</option>
                                <option value="kuliah" {{ request('status_kerja') == 'kuliah' ? 'selected' : '' }}>Kuliah</option>
                                <option value="wirausaha" {{ request('status_kerja') == 'wirausaha' ? 'selected' : '' }}>Wirausaha</option>
                                <option value="menganggur" {{ request('status_kerja') == 'menganggur' ? 'selected' : '' }}>Menganggur</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="sort" class="form-label">Urutkan</label>
                            <select class="form-select" id="sort" name="sort">
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama A-Z</option>
                                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Nama Z-A</option>
                                <option value="tahun_lulus" {{ request('sort') == 'tahun_lulus' ? 'selected' : '' }}>Tahun Lulus</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-1">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i>
                                </button>
                                <a href="{{ route('admin.alumni.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-redo"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Alumni Table -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            Daftar Alumni
                            @if(request('jurusan_id'))
                                - {{ $jurusans->find(request('jurusan_id'))->nama_jurusan ?? '' }}
                            @endif
                        </h5>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                                <i class="fas fa-check-square me-1"></i>Pilih Semua
                            </button>
                            <button class="btn btn-sm btn-outline-success" onclick="bulkAction('verify')" disabled id="bulkVerifyBtn">
                                <i class="fas fa-check me-1"></i>Verifikasi
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="bulkAction('delete')" disabled id="bulkDeleteBtn">
                                <i class="fas fa-trash me-1"></i>Hapus
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($alumni->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40">
                                            <input type="checkbox" class="form-check-input" id="selectAllCheckbox">
                                        </th>
                                        <th>Alumni</th>
                                        <th>Jurusan</th>
                                        <th>Tahun Lulus</th>
                                        <th>Status Kerja</th>
                                        <th>Kontak</th>
                                        <th>Verifikasi</th>
                                        <th width="120">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($alumni as $alumnus)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="form-check-input row-checkbox" value="{{ $alumnus->id }}">
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3">
                                                        {{ strtoupper(substr($alumnus->nama_lengkap, 0, 2)) }}
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $alumnus->nama_lengkap }}</h6>
                                                        <small class="text-muted">NISN: {{ $alumnus->nisn }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $alumnus->jurusan->nama_jurusan ?? '-' }}</span>
                                            </td>
                                            <td>
                                                <strong>{{ $alumnus->tahun_lulus }}</strong>
                                            </td>
                                            <td>
                                                @php
                                                    $statusConfig = [
                                                        'bekerja' => ['class' => 'success', 'icon' => 'briefcase'],
                                                        'kuliah' => ['class' => 'info', 'icon' => 'graduation-cap'],
                                                        'wirausaha' => ['class' => 'warning', 'icon' => 'store'],
                                                        'menganggur' => ['class' => 'secondary', 'icon' => 'user-clock'],
                                                    ];
                                                    $status = $statusConfig[$alumnus->status_kerja] ?? ['class' => 'light', 'icon' => 'question'];
                                                @endphp
                                                <span class="badge bg-{{ $status['class'] }}">
                                                    <i class="fas fa-{{ $status['icon'] }} me-1"></i>
                                                    {{ ucfirst($alumnus->status_kerja) }}
                                                </span>
                                                @if($alumnus->perusahaan)
                                                    <div class="small text-muted mt-1">{{ Str::limit($alumnus->perusahaan, 30) }}</div>
                                                @endif
                                            </td>
                                            <td>
                                                <div>
                                                    <i class="fas fa-envelope text-muted me-1"></i>{{ $alumnus->email }}
                                                </div>
                                                @if($alumnus->no_hp)
                                                    <div>
                                                        <i class="fas fa-phone text-muted me-1"></i>{{ $alumnus->no_hp }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                @if($alumnus->is_verified)
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check me-1"></i>Terverifikasi
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-clock me-1"></i>Pending
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                                            onclick="viewAlumni({{ $alumnus->id }})" title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <a href="{{ route('admin.alumni.edit', $alumnus) }}" 
                                                       class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if(!$alumnus->is_verified)
                                                        <button type="button" class="btn btn-sm btn-outline-success" 
                                                                onclick="verifyAlumni({{ $alumnus->id }})" title="Verifikasi">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    @endif
                                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                                            onclick="deleteAlumni({{ $alumnus->id }}, '{{ $alumnus->nama_lengkap }}')" title="Hapus">
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
                            {{ $alumni->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum Ada Data Alumni</h5>
                            <p class="text-muted">Data alumni akan muncul di sini setelah ditambahkan atau diimpor.</p>
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="{{ route('admin.alumni.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Tambah Alumni
                                </a>
                                <button class="btn btn-outline-info" onclick="importData()">
                                    <i class="fas fa-upload me-2"></i>Import Alumni
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alumni Detail Modal -->
<div class="modal fade" id="alumniDetailModal" tabindex="-1" aria-labelledby="alumniDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="alumniDetailModalLabel">
                    <i class="fas fa-user-graduate me-2"></i>
                    Detail Alumni
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="alumniDetailContent">
                <!-- Alumni details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Statistics Modal -->
<div class="modal fade" id="statisticsModal" tabindex="-1" aria-labelledby="statisticsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statisticsModalLabel">
                    <i class="fas fa-chart-bar me-2"></i>
                    Statistik Alumni
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="statisticsContent">
                <!-- Statistics will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">
                    <i class="fas fa-upload me-2"></i>
                    Import Data Alumni
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.alumni.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="import_file" class="form-label">Pilih File Excel</label>
                        <input type="file" class="form-control" id="import_file" name="import_file" 
                               accept=".xlsx,.xls" required>
                        <div class="form-text">
                            File harus berformat Excel (.xlsx atau .xls). 
                            <a href="{{ asset('template_import_alumni.xlsx') }}" target="_blank">
                                Download template
                            </a>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Petunjuk Import:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Gunakan template yang disediakan</li>
                            <li>Pastikan format data sesuai dengan kolom yang ada</li>
                            <li>Data yang sudah ada akan diupdate jika NISN sama</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-1"></i>
                        Import Data
                    </button>
                </div>
            </form>
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
                <p>Apakah Anda yakin ingin menghapus data alumni "<span id="alumniName"></span>"?</p>
                <p class="text-danger small">
                    <i class="fas fa-warning me-1"></i>
                    Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>
                        Hapus Alumni
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

.card:hover {
    transform: translateY(-2px);
    transition: transform 0.2s;
}
</style>
@endpush

@push('scripts')
<script>
// Filter by jurusan
function filterByJurusan(jurusanId) {
    document.getElementById('jurusan_id').value = jurusanId;
    document.getElementById('filterForm').submit();
}

// View alumni detail
function viewAlumni(alumniId) {
    fetch(`/admin/alumni/${alumniId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const alumni = data.data;
                const modalContent = document.getElementById('alumniDetailContent');
                
                modalContent.innerHTML = `
                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="text-primary mb-3">Data Pribadi</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Nama Lengkap</strong></td>
                                    <td>: ${alumni.nama_lengkap}</td>
                                </tr>
                                <tr>
                                    <td><strong>NISN</strong></td>
                                    <td>: ${alumni.nisn}</td>
                                </tr>
                                <tr>
                                    <td><strong>Jurusan</strong></td>
                                    <td>: ${alumni.jurusan?.nama_jurusan || '-'}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tahun Lulus</strong></td>
                                    <td>: ${alumni.tahun_lulus}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email</strong></td>
                                    <td>: ${alumni.email}</td>
                                </tr>
                                <tr>
                                    <td><strong>No. HP</strong></td>
                                    <td>: ${alumni.no_hp || '-'}</td>
                                </tr>
                                <tr>
                                    <td><strong>Alamat</strong></td>
                                    <td>: ${alumni.alamat || '-'}</td>
                                </tr>
                            </table>
                            
                            <h6 class="text-primary mb-3 mt-4">Status Pekerjaan</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Status Kerja</strong></td>
                                    <td>: ${alumni.status_kerja || '-'}</td>
                                </tr>
                                <tr>
                                    <td><strong>Perusahaan</strong></td>
                                    <td>: ${alumni.perusahaan || '-'}</td>
                                </tr>
                                <tr>
                                    <td><strong>Posisi</strong></td>
                                    <td>: ${alumni.posisi || '-'}</td>
                                </tr>
                                <tr>
                                    <td><strong>Gaji</strong></td>
                                    <td>: ${alumni.gaji ? 'Rp ' + new Intl.NumberFormat('id-ID').format(alumni.gaji) : '-'}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-primary mb-3">Status Verifikasi</h6>
                            <div class="text-center">
                                <div class="mb-3">
                                    ${alumni.is_verified ? 
                                        '<span class="badge bg-success fs-6"><i class="fas fa-check me-2"></i>Terverifikasi</span>' :
                                        '<span class="badge bg-warning fs-6"><i class="fas fa-clock me-2"></i>Pending</span>'
                                    }
                                </div>
                                ${!alumni.is_verified ? 
                                    `<button class="btn btn-success btn-sm" onclick="verifyAlumni(${alumni.id})">
                                        <i class="fas fa-check me-1"></i>Verifikasi Sekarang
                                    </button>` : ''
                                }
                            </div>
                            
                            <div class="mt-4">
                                <h6 class="text-primary mb-3">CV & Dokumen</h6>
                                ${alumni.cv_path ? 
                                    `<a href="/alumni/cv/${alumni.id}/preview" target="_blank" class="btn btn-outline-primary btn-sm w-100 mb-2">
                                        <i class="fas fa-file-pdf me-1"></i>Lihat CV
                                    </a>` : 
                                    '<p class="text-muted small">CV belum diupload</p>'
                                }
                            </div>
                            
                            <div class="mt-3">
                                <small class="text-muted">Terdaftar: ${new Date(alumni.created_at).toLocaleDateString('id-ID')}</small>
                            </div>
                        </div>
                    </div>
                `;
                
                const modal = new bootstrap.Modal(document.getElementById('alumniDetailModal'));
                modal.show();
            } else {
                alert('Gagal memuat detail alumni');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memuat data');
        });
}

// Verify alumni
function verifyAlumni(alumniId) {
    if (confirm('Verifikasi data alumni ini?')) {
        fetch(`/admin/alumni/${alumniId}/verify`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Gagal memverifikasi alumni');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan');
        });
    }
}

// Delete alumni
function deleteAlumni(alumniId, alumniName) {
    document.getElementById('alumniName').textContent = alumniName;
    document.getElementById('deleteForm').action = `/admin/alumni/${alumniId}`;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Show statistics
function showStatistics() {
    fetch('/admin/alumni/statistics')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const stats = data.data;
                const modalContent = document.getElementById('statisticsContent');
                
                // Create statistics charts and tables here
                modalContent.innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Alumni per Jurusan</h6>
                            <div id="jurusanChart"></div>
                        </div>
                        <div class="col-md-6">
                            <h6>Status Pekerjaan</h6>
                            <div id="statusChart"></div>
                        </div>
                    </div>
                `;
                
                const modal = new bootstrap.Modal(document.getElementById('statisticsModal'));
                modal.show();
            }
        });
}

// Import data
function importData() {
    const modal = new bootstrap.Modal(document.getElementById('importModal'));
    modal.show();
}

// Export data
function exportData() {
    window.location.href = '/admin/alumni/export?' + new URLSearchParams(window.location.search);
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
    const bulkVerifyBtn = document.getElementById('bulkVerifyBtn');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    
    if (selectedCheckboxes.length > 0) {
        bulkVerifyBtn.disabled = false;
        bulkDeleteBtn.disabled = false;
        bulkVerifyBtn.textContent = `Verifikasi ${selectedCheckboxes.length} item`;
        bulkDeleteBtn.textContent = `Hapus ${selectedCheckboxes.length} item`;
    } else {
        bulkVerifyBtn.disabled = true;
        bulkDeleteBtn.disabled = true;
        bulkVerifyBtn.innerHTML = '<i class="fas fa-check me-1"></i>Verifikasi';
        bulkDeleteBtn.innerHTML = '<i class="fas fa-trash me-1"></i>Hapus';
    }
}

function bulkAction(action) {
    const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
    const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);
    
    if (selectedIds.length === 0) {
        alert('Pilih minimal satu alumni');
        return;
    }
    
    if (action === 'verify') {
        if (confirm(`Verifikasi ${selectedIds.length} alumni?`)) {
            // Implementation for bulk verify
            console.log('Bulk verify:', selectedIds);
        }
    } else if (action === 'delete') {
        if (confirm(`Hapus ${selectedIds.length} alumni?`)) {
            // Implementation for bulk delete
            console.log('Bulk delete:', selectedIds);
        }
    }
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
