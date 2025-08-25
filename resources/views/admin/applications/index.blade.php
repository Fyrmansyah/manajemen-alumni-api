@extends('layouts.app')

@section('title','Kelola Aplikasi - Admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h4 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Kelola Lamaran</h4>
                    <span class="badge bg-light text-dark">{{ $applications->total() }} Total Lamaran</span>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.applications.index') }}" class="row g-2 mb-3">
                        <div class="col-md-3 col-sm-6">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                <option value="submitted" {{ request('status')==='submitted'?'selected':'' }}>Dikirim</option>
                                <option value="reviewed" {{ request('status')==='reviewed'?'selected':'' }}>Direview</option>
                                <option value="interview" {{ request('status')==='interview'?'selected':'' }}>Interview</option>
                                <option value="accepted" {{ request('status')==='accepted'?'selected':'' }}>Diterima</option>
                                <option value="rejected" {{ request('status')==='rejected'?'selected':'' }}>Ditolak</option>
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <select name="job_id" class="form-select" onchange="this.form.submit()">
                                <option value="">Semua Lowongan</option>
                                                @foreach(($jobs ?? []) as $jobItem)
                                                    @php if(!is_object($jobItem)) continue; @endphp
                                                    <option value="{{ $jobItem->id }}" {{ request('job_id')==$jobItem->id?'selected':'' }}>{{ $jobItem->title }}</option>
                                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 col-sm-8">
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari alumni / perusahaan">
                        </div>
                        <div class="col-md-2 col-sm-4 d-flex gap-2">
                            <button class="btn btn-light flex-grow-1" type="submit"><i class="fas fa-search"></i></button>
                            @if(request()->hasAny(['search','status','job_id']))
                                <a href="{{ route('admin.applications.index') }}" class="btn btn-outline-secondary" title="Reset"><i class="fas fa-times"></i></a>
                            @endif
                        </div>
                    </form>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($applications->count())
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
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
                                                        $fotoUrl=null;
                                                        if($foto){ $rel=str_starts_with($foto,'alumni_photos/')?$foto:'alumni_photos/'.ltrim($foto,'/'); $fotoUrl=asset('storage/'.$rel);} 
                                                    @endphp
                                                    @if($fotoUrl)
                                                        <img src="{{ $fotoUrl }}" alt="Foto" style="width:100%;height:100%;object-fit:cover;">
                                                    @else
                                                        {{ strtoupper(substr($application->alumni->nama ?? $application->alumni->nama_lengkap ?? 'A',0,1)) }}
                                                    @endif
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $application->alumni->nama ?? $application->alumni->nama_lengkap ?? 'Nama tidak tersedia' }}</h6>
                                                    <small class="text-muted">{{ $application->alumni->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>{{ $application->job->title }}</strong><br>
                                            <small class="text-muted">{{ $application->job->job_type }}</small>
                                        </td>
                                        <td>
                                            {{ $application->applied_at ? $application->applied_at->format('d M Y') : 'Tidak diketahui' }}<br>
                                            <small class="text-muted">{{ $application->applied_at ? $application->applied_at->format('H:i') : '' }}</small>
                                        </td>
                                        <td>
                                            @php
                                                $statusMap=[
                                                    'submitted'=>['Dikirim','info'],
                                                    'reviewed'=>['Direview','warning text-dark'],
                                                    'interview'=>['Interview','primary'],
                                                    'accepted'=>['Diterima','success'],
                                                    'rejected'=>['Ditolak','danger'],
                                                ];
                                                [$label,$cls]=$statusMap[$application->status]??[ucfirst($application->status),'secondary'];
                                            @endphp
                                            <span class="badge bg-{{ $cls }}">{{ $label }}</span>
                                        </td>
                                        <td>
                                            @if($application->cv_file)
                                                <a href="{{ asset('storage/cvs/'.$application->cv_file) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-file-pdf"></i> Lihat CV</a>
                                            @else
                                                <span class="text-muted">Tidak ada CV</span>
                                            @endif
                                        </td>
                                                                                <td>
                                                                                    @if(in_array($application->status,['accepted','rejected']))
                                                                                        <span class="badge bg-{{ $application->status==='accepted'?'success':'danger' }}">
                                                                                            {{ $application->status==='accepted'?'Diterima':'Ditolak' }}
                                                                                        </span>
                                                                                    @else
                                                                                        <div class="d-flex gap-2">
                                                                                            <a href="{{ route('admin.applications.show',$application) }}" class="btn btn-icon btn-outline-primary" title="Detail">
                                                                                                <i class="fas fa-eye"></i>
                                                                                            </a>
                                                                                            <button type="button" class="btn btn-icon btn-outline-success" title="Ubah Status" data-bs-toggle="modal" data-bs-target="#statusModal"
                                                                                                data-id="{{ $application->id }}"
                                                                                                data-status="{{ $application->status }}"
                                                                                                data-interview_at="{{ $application->interview_at ? $application->interview_at->format('Y-m-d\\TH:i') : '' }}"
                                                                                                data-interview_location="{{ $application->interview_location }}"
                                                                                                data-interview_details="{{ $application->interview_details }}">
                                                                                                <i class="fas fa-check"></i>
                                                                                            </button>
                                                                                            <form action="{{ route('admin.applications.updateStatus',$application) }}" method="POST" onsubmit="return confirm('Tolak lamaran ini?')" class="m-0">
                                                                                                @csrf @method('PATCH')
                                                                                                <input type="hidden" name="status" value="rejected">
                                                                                                <button type="submit" class="btn btn-icon btn-outline-danger" title="Tolak">
                                                                                                    <i class="fas fa-times"></i>
                                                                                                </button>
                                                                                            </form>
                                                                                        </div>
                                                                                    @endif
                                                                                </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-3">{{ $applications->appends(request()->query())->links() }}</div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum Ada Lamaran</h5>
                            <p class="text-muted">Lamaran akan muncul di sini ketika alumni melamar pekerjaan.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Unified Status Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ubah Status Lamaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="statusForm">
                @csrf @method('PATCH')
                <div class="modal-body">
                    <input type="hidden" name="_status_current" id="statusCurrent">
                    <div class="mb-3">
                        <label class="form-label">Status Baru</label>
                        <select name="status" id="statusSelect" class="form-select" required>
                            <option value="reviewed">Direview</option>
                            <option value="interview">Interview</option>
                            <option value="accepted">Diterima</option>
                            <option value="rejected">Ditolak</option>
                        </select>
                    </div>
                    <div id="interviewFields" class="d-none">
                        <div class="mb-3">
                            <label class="form-label">Tanggal & Waktu Interview</label>
                            <input type="datetime-local" name="interview_at" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lokasi / Media</label>
                            <input type="text" name="interview_location" class="form-control" placeholder="Online / Kantor ...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Detail Tambahan</label>
                            <textarea name="interview_details" class="form-control" rows="3" placeholder="Instruksi tambahan"></textarea>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan (Opsional)</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Catatan untuk alumni"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const statusModal=document.getElementById('statusModal');
statusModal?.addEventListener('show.bs.modal',ev=>{
    const btn=ev.relatedTarget; if(!btn) return; const id=btn.getAttribute('data-id');
    const form=document.getElementById('statusForm');
    form.action=`/admin/applications/${id}/status`;
    const current=btn.getAttribute('data-status');
    document.getElementById('statusCurrent').value=current;
    const select=document.getElementById('statusSelect');
    select.value = current==='submitted' ? 'reviewed' : (current==='reviewed' ? 'interview' : current);
    // Prefill interview fields
    form.querySelector('[name="interview_at"]').value=btn.getAttribute('data-interview_at')||'';
    form.querySelector('[name="interview_location"]').value=btn.getAttribute('data-interview_location')||'';
    form.querySelector('[name="interview_details"]').value=btn.getAttribute('data-interview_details')||'';
    toggleInterview(select.value);
});

document.getElementById('statusSelect')?.addEventListener('change',e=>{
    toggleInterview(e.target.value);
});

function toggleInterview(val){
    const fields=document.getElementById('interviewFields');
    if(val==='interview'){ fields.classList.remove('d-none'); }
    else { fields.classList.add('d-none'); }
}
</script>
@endpush

@push('styles')
<style>
.avatar-sm{width:40px;height:40px;font-size:16px;font-weight:600;}
.table td{vertical-align:middle;}
.badge{font-size:0.75rem;}
.action-buttons .btn-icon{display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;padding:0;border-width:1px;}
.action-buttons .btn-icon i{font-size:14px;}
.action-buttons form{display:inline-block;}
.btn-icon{width:34px;height:34px;display:inline-flex;align-items:center;justify-content:center;padding:0;}
.btn-icon i{font-size:14px;}
</style>
@endpush
