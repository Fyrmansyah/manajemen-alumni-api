@extends('layouts.app')

@section('title', 'Kelola Lamaran')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-clipboard-list me-2"></i>
                            Kelola Lamaran
                        </h4>
                        <span class="badge bg-light text-dark">
                            {{ $applications->total() }} Total Lamaran
                        </span>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('company.applications') }}" class="d-flex gap-2">
                                <select name="status" class="form-select" onchange="this.form.submit()">
                                    <option value="">Semua Status</option>
                                    <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Dikirim</option>
                                    <option value="reviewed" {{ request('status') === 'reviewed' ? 'selected' : '' }}>Direview</option>
                                    <option value="interview" {{ request('status') === 'interview' ? 'selected' : '' }}>Interview</option>
                                    <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Diterima</option>
                                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                                
                                <select name="job_id" class="form-select" onchange="this.form.submit()">
                                    <option value="">Semua Lowongan</option>
                                    @foreach($jobs as $job)
                                        <option value="{{ $job->id }}" {{ request('job_id') == $job->id ? 'selected' : '' }}>
                                            {{ $job->title }}
                                        </option>
                                    @endforeach
                                </select>
                                
                                @if(request()->hasAny(['status', 'job_id']))
                                    <a href="{{ route('company.applications') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Reset
                                    </a>
                                @endif
                            </form>
                        </div>
                    </div>

                    <!-- Applications Table -->
                    @if($applications->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Alumni</th>
                                        <th>Posisi</th>
                                        <th>Tanggal Melamar</th>
                                        <th>Status</th>
                                        <th>CV</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($applications as $application)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3">
                                                        {{ strtoupper(substr($application->alumni->nama ?? $application->alumni->nama_lengkap ?? 'A', 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $application->alumni->nama ?? $application->alumni->nama_lengkap ?? 'Nama tidak tersedia' }}</h6>
                                                        <small class="text-muted">{{ $application->alumni->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <strong>{{ $application->job->title }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $application->job->job_type }}</small>
                                            </td>
                                            <td>
                                                {{ $application->applied_at ? $application->applied_at->format('d M Y') : 'Tidak diketahui' }}
                                                <br>
                                                <small class="text-muted">{{ $application->applied_at ? $application->applied_at->format('H:i') : '' }}</small>
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass = match($application->status) {
                                                        'submitted' => 'bg-info',
                                                        'reviewed' => 'bg-warning',
                                                        'interview' => 'bg-primary',
                                                        'accepted' => 'bg-success',
                                                        'rejected' => 'bg-danger',
                                                        default => 'bg-secondary'
                                                    };
                                                    
                                                    $statusText = match($application->status) {
                                                        'submitted' => 'Dikirim',
                                                        'reviewed' => 'Direview',
                                                        'interview' => 'Interview',
                                                        'accepted' => 'Diterima',
                                                        'rejected' => 'Ditolak',
                                                        default => 'Tidak diketahui'
                                                    };
                                                @endphp
                                                <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                                            </td>
                                            <td>
                                                @if($application->cv_file)
                                                    <a href="{{ asset('storage/cvs/' . $application->cv_file) }}" 
                                                       target="_blank" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-file-pdf"></i> Lihat CV
                                                    </a>
                                                @else
                                                    <span class="text-muted">Tidak ada CV</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-primary" 
                                                            onclick="viewApplication({{ $application->id }})">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    
                                                    @if($application->status !== 'accepted' && $application->status !== 'rejected')
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-success" 
                                                                onclick="updateStatus({{ $application->id }}, 'accepted')">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-danger" 
                                                                onclick="updateStatus({{ $application->id }}, 'rejected')">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $applications->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum Ada Lamaran</h5>
                            <p class="text-muted">Lamaran akan muncul di sini ketika ada alumni yang melamar ke lowongan Anda.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Application Detail Modal -->
<div class="modal fade" id="applicationModal" tabindex="-1" aria-labelledby="applicationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="applicationModalLabel">Detail Lamaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="applicationModalBody">
                <!-- Application details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">Update Status Lamaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="statusForm">
                <div class="modal-body">
                    <input type="hidden" id="applicationId">
                    <div class="mb-3">
                        <label for="newStatus" class="form-label">Status Baru</label>
                        <select class="form-select" id="newStatus" name="status" required>
                            <option value="">Pilih Status</option>
                            <option value="reviewed">Direview</option>
                            <option value="interview">Interview</option>
                            <option value="accepted">Diterima</option>
                            <option value="rejected">Ditolak</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Catatan (Opsional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Tambahkan catatan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.avatar-sm {
    width: 40px;
    height: 40px;
    font-size: 16px;
    font-weight: bold;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

.btn-group .btn {
    margin-right: 2px;
}

.badge {
    font-size: 0.75em;
}
</style>
@endpush

@push('scripts')
<script>
function viewApplication(applicationId) {
    fetch(`/api/applications/${applicationId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const app = data.data;
                const modalBody = document.getElementById('applicationModalBody');
                
                modalBody.innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Informasi Alumni</h6>
                            <p><strong>Nama:</strong> ${app.alumni.nama || app.alumni.nama_lengkap || 'Tidak tersedia'}</p>
                            <p><strong>Email:</strong> ${app.alumni.email}</p>
                            <p><strong>No. Telepon:</strong> ${app.alumni.phone || app.alumni.no_tlp || 'Tidak tersedia'}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Informasi Lamaran</h6>
                            <p><strong>Posisi:</strong> ${app.job.title}</p>
                            <p><strong>Tanggal Melamar:</strong> ${new Date(app.applied_at).toLocaleDateString('id-ID')}</p>
                            <p><strong>Status:</strong> <span class="badge bg-primary">${app.status}</span></p>
                        </div>
                    </div>
                    <hr>
                    <h6>Cover Letter</h6>
                    <p style="white-space: pre-wrap;">${app.cover_letter}</p>
                    ${app.cv_file ? `
                    <hr>
                    <h6>CV</h6>
                    <a href="/storage/cvs/${app.cv_file}" target="_blank" class="btn btn-primary">
                        <i class="fas fa-file-pdf me-2"></i>Lihat CV
                    </a>
                    ` : ''}
                `;
                
                const modal = new bootstrap.Modal(document.getElementById('applicationModal'));
                modal.show();
            } else {
                alert('Gagal memuat detail lamaran');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memuat detail lamaran');
        });
}

function updateStatus(applicationId, status) {
    document.getElementById('applicationId').value = applicationId;
    document.getElementById('newStatus').value = status;
    
    const modal = new bootstrap.Modal(document.getElementById('statusModal'));
    modal.show();
}

document.getElementById('statusForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const applicationId = document.getElementById('applicationId').value;
    const formData = new FormData(this);
    
    fetch(`/company/applications/${applicationId}/status`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            status: formData.get('status'),
            notes: formData.get('notes')
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Gagal mengupdate status: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat mengupdate status');
    });
});
</script>
@endpush
