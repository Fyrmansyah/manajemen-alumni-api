@extends('layouts.app')

@section('title','Lowongan Tersimpan')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4 mb-0 text-gray-800"><i class="fas fa-bookmark me-2"></i>Lowongan Tersimpan</h1>
    <a href="{{ route('alumni.dashboard') }}" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left me-1"></i>Kembali</a>
  </div>

  <div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-column flex-md-row gap-3 justify-content-between align-items-start align-items-md-center">
        <div class="fw-bold text-primary"><i class="fas fa-list me-1"></i>Daftar Lowongan</div>
        <form method="GET" class="d-flex" style="max-width:320px;">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm me-2" placeholder="Cari judul / perusahaan...">
            <button class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
        </form>
    </div>
    <div class="card-body p-0">
        @if($jobs->count())
            <div class="list-group list-group-flush">
                @foreach($jobs as $job)
                    <div class="list-group-item p-3">
                        <div class="d-flex">
                            <div class="me-3">
                                @if($job->company->logo)
                                    <img src="{{ asset('storage/company_logos/' . $job->company->logo) }}" alt="{{ $job->company->company_name }}" class="rounded" style="width:48px;height:48px;object-fit:cover;">
                                @else
                                    <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                                        {{ strtoupper(substr($job->company->company_name,0,1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <h5 class="mb-1 fw-bold"><a href="{{ route('jobs.show',$job->id) }}" class="text-decoration-none">{{ $job->title }}</a></h5>
                                    <span class="badge bg-{{ $job->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($job->status) }}</span>
                                </div>
                                <div class="text-muted small mb-1">{{ $job->company->company_name }} &bull; {{ $job->location }}</div>
                                @php $deadline = $job->getDeadlineInfo(); @endphp
                                @if($deadline)
                                    <div class="small {{ $deadline['css_class'] }}">{{ $deadline['text'] }}</div>
                                @endif
                                <div class="small text-muted mt-1">Disimpan {{ $job->pivot->created_at->diffForHumans() }}</div>
                            </div>
                            <div class="ms-3 d-flex flex-column align-items-end">
                                <a href="{{ route('jobs.show',$job->id) }}" class="btn btn-sm btn-outline-primary mb-2"><i class="fas fa-eye me-1"></i>Lihat</a>
                                @if($job->canApply() && !$job->applications()->where('alumni_id',auth('alumni')->id())->exists())
                                    <button class="btn btn-sm btn-success" onclick="openApplyModal({{ $job->id }}, '{{ addslashes($job->title) }}')"><i class="fas fa-paper-plane me-1"></i>Lamar</button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="p-3">
                {{ $jobs->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-bookmark fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Belum ada lowongan tersimpan</h5>
                <p class="text-muted">Temukan dan simpan lowongan menarik untuk melamar nanti.</p>
                <a href="{{ route('jobs.index') }}" class="btn btn-primary"><i class="fas fa-search me-1"></i>Cari Lowongan</a>
            </div>
        @endif
    </div>
  </div>
</div>

<!-- Apply Modal -->
<div class="modal fade" id="applySavedModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-paper-plane me-2"></i>Lamar Pekerjaan <span id="applyJobTitle" class="text-primary"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="applyModalBody">
                <form id="applyForm" onsubmit="event.preventDefault(); submitApplication();">
                        <div class="mb-3">
                                <label class="form-label">Cover Letter <span class="text-danger">*</span></label>
                                <textarea name="cover_letter" id="cover_letter" class="form-control" rows="6" maxlength="2000" placeholder="Tulis cover letter (min 50 karakter)..." oninput="updateCharCount(this)" required></textarea>
                                <div class="form-text"><span id="charCount">0</span>/2000 karakter</div>
                        </div>
                        <div class="mb-3">
                                <label class="form-label">Upload CV (Opsional)</label>
                                <input type="file" name="cv_file" id="cv_file" class="form-control" accept=".pdf,.doc,.docx">
                                <div class="form-text">PDF/DOC/DOCX maks 2MB.</div>
                        </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i>Batal</button>
                <button type="button" id="submitApplicationBtn" class="btn btn-primary" onclick="submitApplication()"><i class="fas fa-paper-plane me-1"></i>Kirim Lamaran</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentJobId = null;
function openApplyModal(jobId,title){
    currentJobId = jobId;
    document.getElementById('applyJobTitle').textContent = ' - ' + title;
    const modal = new bootstrap.Modal(document.getElementById('applySavedModal'));
    modal.show();
}
function updateCharCount(el){
    const c = el.value.length;const span=document.getElementById('charCount');span.textContent=c;span.className=c<50?'text-danger':(c>1900?'text-danger':(c>1800?'text-warning':'text-success'));
}
function validateForm(){
    const cover=document.getElementById('cover_letter').value.trim();
    const cv=document.getElementById('cv_file').files[0];
    if(cover.length<50){alert('Cover letter minimal 50 karakter');return false;}
    if(cover.length>2000){alert('Cover letter maksimal 2000');return false;}
    if(cv && cv.size>2*1024*1024){alert('File CV maksimal 2MB');return false;}
    return true;
}
function submitApplication(){
    if(!validateForm()) return;
    if(!currentJobId) return;
    const btn=document.getElementById('submitApplicationBtn');
    const original=btn.innerHTML;btn.disabled=true;btn.innerHTML='<i class="fas fa-spinner fa-spin me-2"></i>Mengirim...';
    const form=document.getElementById('applyForm');
    const fd=new FormData(form);
    fetch(`/jobs/${currentJobId}/apply`,{method:'POST',body:fd,headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json','X-Requested-With':'XMLHttpRequest'}})
        .then(async r=>{const ct=r.headers.get('content-type')||'';const data=ct.includes('json')?await r.json():{};if(r.ok && data.success){
                document.getElementById('applyModalBody').innerHTML=`<div class="text-center py-4"><i class='fas fa-check-circle text-success fa-3x mb-3'></i><h5 class='text-success'>Lamaran Berhasil Dikirim!</h5><p class='text-muted'>Terima kasih telah melamar.</p></div>`;
                document.querySelector('#applySavedModal .modal-footer').innerHTML='<button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="location.reload()"><i class="fas fa-check me-1"></i>Tutup</button>';
        } else {
                alert((data && data.message) || 'Gagal mengirim lamaran');btn.disabled=false;btn.innerHTML=original;}
        })
        .catch(()=>{alert('Terjadi kesalahan');btn.disabled=false;btn.innerHTML=original;});
}
document.getElementById('applySavedModal').addEventListener('hidden.bs.modal',()=>{
    const form=document.getElementById('applyForm');if(form){form.reset();updateCharCount(document.getElementById('cover_letter'));}
    const footer=document.querySelector('#applySavedModal .modal-footer');footer.innerHTML='<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i>Batal</button><button type="button" id="submitApplicationBtn" class="btn btn-primary" onclick="submitApplication()"><i class="fas fa-paper-plane me-1"></i>Kirim Lamaran</button>';
    document.getElementById('applyModalBody').innerHTML=document.getElementById('applyModalBody').innerHTML; // already reset via form
});
</script>
@endpush
@endsection
