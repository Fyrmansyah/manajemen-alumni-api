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
                                                    <div class="avatar-sm rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3 overflow-hidden" style="width:40px;height:40px;">
                                                        @php
                                                            $foto=$application->alumni->foto??null;
                                                            if($foto){
                                                                $rel = str_starts_with($foto,'alumni_photos/') ? $foto : 'alumni_photos/' . ltrim($foto,'/');
                                                                $fotoUrl = asset('storage/'.$rel);
                                                            }
                                                        @endphp
                                                        @if(!empty($fotoUrl))
                                                            <img src="{{ $fotoUrl }}" alt="Foto" style="width:100%;height:100%;object-fit:cover;">
                                                        @else
                                                            {{ strtoupper(substr($application->alumni->nama ?? $application->alumni->nama_lengkap ?? 'A', 0, 1)) }}
                                                        @endif
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
                                                @if($application->status === 'accepted')
                                                    <span class="badge bg-success">Diterima</span>
                                                @elseif($application->status === 'rejected')
                                                    <span class="badge bg-danger">Ditolak</span>
                                                @else
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-sm btn-outline-primary" title="Detail" onclick="viewApplication({{ $application->id }})"><i class="fas fa-eye"></i></button>
                                                        <button type="button" class="btn btn-sm btn-outline-success" title="Ubah Status" onclick="openStatusModal({{ $application->id }})"><i class="fas fa-check"></i></button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" title="Tolak" onclick="rejectQuick({{ $application->id }})"><i class="fas fa-times"></i></button>
                                                    </div>
                                                @endif
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
            <!-- Footer with schedule interview button removed per latest requirement -->
            <div class="modal-footer d-none" id="applicationInterviewFooter" style="display:none !important;"></div>
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
                    <div class="mb-3 d-none interview-fields" id="interviewFields">
                        <label class="form-label">Detail Interview</label>
                        <div class="mb-2">
                            <label class="form-label small mb-1" for="interview_at">Tanggal & Waktu</label>
                            <input type="datetime-local" class="form-control" id="interview_at" name="interview_at">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small mb-1" for="interview_media">Media</label>
                            <select class="form-select" id="interview_media" name="interview_media">
                                <option value="">Pilih Media</option>
                                <option value="Offline">Offline / Onsite</option>
                                <option value="Zoom">Zoom</option>
                                <option value="Google Meet">Google Meet</option>
                                <option value="Microsoft Teams">Microsoft Teams</option>
                                <option value="Telepon">Telepon</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small mb-1" for="interview_location">Lokasi / Link</label>
                            <input type="text" class="form-control" id="interview_location" name="interview_location" placeholder="Alamat atau URL meeting">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small mb-1" for="interview_details">Catatan Interview</label>
                            <textarea class="form-control" id="interview_details" name="interview_details" rows="2" placeholder="Instruksi tambahan..."></textarea>
                        </div>
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

@push('head-scripts')
<script>
// Awal stub agar onclick tidak error sebelum script utama termuat
window.viewApplication = window.viewApplication || function(id){ console.warn('Memuat skrip...'); };
window.updateStatus = window.updateStatus || function(){ console.warn('Memuat skrip...'); };
window.toggleInterviewFields = window.toggleInterviewFields || function(){};
</script>
@endpush

@push('scripts')
<script>
console.log('applications.blade main script start');
(function(){
    // Helper to build HTML safely
    function escapeHtml(str){
        return (str||'').toString()
            .replace(/&/g,'&amp;')
            .replace(/</g,'&lt;')
            .replace(/>/g,'&gt;');
    }

    window.viewApplication = function(applicationId) {
        fetch(`/company/applications/${applicationId}/detail`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(r=>{ if(!r.ok) throw new Error('HTTP '+r.status); return r.json(); })
        .then(data=>{
            if(!data.success || !data.application) throw new Error(data.message||'Gagal memuat detail');
            const app = data.application;
            let foto = app.alumni.foto || '';
            if(foto && !foto.startsWith('alumni_photos/')) foto = 'alumni_photos/' + foto.replace(/^\//,'');
            const photoTag = foto ? '<img src="/storage/'+foto+'" alt="Foto" style="width:100%;height:100%;object-fit:cover;">' : "<i class='fas fa-user text-muted fa-2x'></i>";
            const jurusanNama = (app.alumni.jurusan && app.alumni.jurusan.nama) ? app.alumni.jurusan.nama : '-';
            let interviewBlock = '';
            if(app.interview_at){
                interviewBlock = '<hr><h6>Detail Interview</h6>'+
                    '<p class="mb-1"><strong>Waktu:</strong> '+escapeHtml(app.interview_at)+'</p>'+
                    (app.interview_media ? '<p class="mb-1"><strong>Media:</strong> '+escapeHtml(app.interview_media)+'</p>':'')+
                    (app.interview_location ? '<p class="mb-1"><strong>Lokasi/Link:</strong> '+escapeHtml(app.interview_location)+'</p>':'')+
                    (app.interview_details ? '<p class="mb-1"><strong>Catatan:</strong> '+escapeHtml(app.interview_details)+'</p>':'');
            }
            const cvBlock = app.cv_url ? '<h6>CV</h6><a href="'+app.cv_url+'" target="_blank" class="btn btn-outline-primary mb-3"><i class="fas fa-file-pdf me-2"></i>Lihat CV</a>' : '';
            const b = document.getElementById('applicationModalBody');
            b.innerHTML = ''+
                '<div class="d-flex align-items-center gap-3 mb-3">'+
                    '<div class="rounded-circle bg-light overflow-hidden d-flex align-items-center justify-content-center" style="width:80px;height:80px;">'+photoTag+'</div>'+
                    '<div>'+
                        '<h5 class="mb-1">'+escapeHtml(app.alumni.nama || app.alumni.name || 'Tidak tersedia')+'</h5>'+
                        '<div class="text-muted small">'+escapeHtml(app.alumni.email)+' • '+escapeHtml(app.alumni.phone||'')+'</div>'+
                    '</div>'+
                '</div>'+
                '<div class="row">'+
                    '<div class="col-md-6">'+
                        '<h6>Informasi Alumni</h6>'+
                        '<p class="mb-1"><strong>Nama:</strong> '+escapeHtml(app.alumni.nama || app.alumni.name || 'Tidak tersedia')+'</p>'+
                        '<p class="mb-1"><strong>Email:</strong> '+escapeHtml(app.alumni.email)+'</p>'+
                        '<p class="mb-1"><strong>No. Telepon:</strong> '+escapeHtml(app.alumni.phone||'Tidak tersedia')+'</p>'+
                        '<p class="mb-1"><strong>Jurusan:</strong> '+escapeHtml(jurusanNama)+'</p>'+
                        '<p class="mb-1"><strong>Tahun Lulus:</strong> '+escapeHtml(app.alumni.tahun_lulus || '-')+'</p>'+
                        '<p class="mb-1"><strong>NISN:</strong> '+escapeHtml(app.alumni.nisn || '-')+'</p>'+
                    '</div>'+ // close first column
                    '<div class="col-md-6">'+
                        '<h6>Informasi Lamaran</h6>'+
                        '<p class="mb-1"><strong>Posisi:</strong> '+escapeHtml(app.job.title)+'</p>'+
                        '<p class="mb-1"><strong>Tanggal Melamar:</strong> '+escapeHtml(app.applied_at || app.created_at)+'</p>'+
                        '<p class="mb-1"><strong>Status:</strong> <span class="badge bg-primary text-uppercase">'+escapeHtml(app.status)+'</span></p>'+
                        (app.interview_at ? '<p class="mb-1"><strong>Interview:</strong> '+escapeHtml(app.interview_at)+(app.interview_media ? ' ('+escapeHtml(app.interview_media)+')':'')+'</p>' : '')+
                    '</div>'+
                '</div>'+
                '<hr>'+
                '<h6>Cover Letter</h6>'+
                '<div class="border rounded p-3 mb-3" style="white-space:pre-wrap;">'+escapeHtml(app.cover_letter || '-')+'</div>'+
                cvBlock +
                interviewBlock;

            new bootstrap.Modal(document.getElementById('applicationModal')).show();
        })
        .catch(err=>{
            console.error('Error loading application detail:', err);
            alert('Terjadi kesalahan saat memuat detail lamaran: '+err.message);
        });
    };
})();
console.log('applications.blade main script loaded');

window.updateStatus = function(applicationId, status) {
    // For quick accept/reject, directly call the API
    if (status === 'accepted' || status === 'rejected') {
        if (!confirm(`Apakah Anda yakin ingin ${status === 'accepted' ? 'menerima' : 'menolak'} lamaran ini?`)) {
            return;
        }
        
        fetch(`/company/applications/${applicationId}/status`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                status: status,
                notes: null
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Gagal: ' + (data.message || 'Terjadi kesalahan'));
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Terjadi kesalahan saat mengupdate status');
        });
    } else {
        // For other statuses, open the modal
        document.getElementById('applicationId').value = applicationId;
        document.getElementById('newStatus').value = status;
        window.toggleInterviewFields(status === 'interview');
        new bootstrap.Modal(document.getElementById('statusModal')).show();
    }
};

// Buka modal status (check button)
function openStatusModal(id){
    document.getElementById('applicationId').value = id;
    document.getElementById('newStatus').value = '';
    toggleInterviewFields(false);
    new bootstrap.Modal(document.getElementById('statusModal')).show();
}

// Tolak cepat (ikon X)
function rejectQuick(id){
    if(!confirm('Tolak lamaran ini?')) return;
    fetch(`/company/applications/${id}/status`, {
        method:'PUT',
        headers:{
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type':'application/json'
        },
        body: JSON.stringify({ status:'rejected' })
    }).then(r=>r.json()).then(d=>{
        if(d.success){ location.reload(); } else { alert('Gagal: '+(d.message||'Tidak diketahui')); }
    }).catch(e=>{ console.error(e); alert('Kesalahan jaringan'); });
}

document.getElementById('statusForm').addEventListener('submit', function(e){
    e.preventDefault();
    const id = document.getElementById('applicationId').value;
    const fd = new FormData(this);
    
    const requestData = {
        status: fd.get('status'),
        notes: fd.get('notes')
    };
    
    // Add interview fields if status is interview
    if (fd.get('status') === 'interview') {
        requestData.interview_at = fd.get('interview_at');
        requestData.interview_location = fd.get('interview_location');
        requestData.interview_details = fd.get('interview_details');
        requestData.interview_media = fd.get('interview_media');
    }
    
    fetch(`/company/applications/${id}/status`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(requestData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('statusModal')).hide();
            location.reload();
        } else {
            alert('Gagal: ' + (data.message || 'Terjadi kesalahan'));
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Terjadi kesalahan saat mengupdate status');
    });
});

document.getElementById('newStatus').addEventListener('change', function(){
    window.toggleInterviewFields(this.value === 'interview');
});

window.toggleInterviewFields = function(show){
    const el = document.getElementById('interviewFields');
    if(show){
        el.classList.remove('d-none');
        if(!document.getElementById('interview_at').value){
            const d = new Date(); d.setDate(d.getDate()+2); d.setHours(9,0,0,0);
            document.getElementById('interview_at').value = d.toISOString().slice(0,16);
        }
    } else { el.classList.add('d-none'); }
};

// Removed prefillInterviewFromDetail functionality along with schedule button

// Auto open via ?application=ID
(function(){
    const p = new URLSearchParams(window.location.search);
    if(p.has('application')){ window.viewApplication(p.get('application')); }
})();
</script>
@endpush