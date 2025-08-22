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
                                    <button class="btn btn-sm btn-success" onclick="applyJob({{ $job->id }})"><i class="fas fa-paper-plane me-1"></i>Lamar</button>
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

@push('scripts')
<script>
function applyJob(id){
    fetch(`/jobs/${id}/apply`, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}})
        .then(r=>r.json())
        .then(d=>{ if(d.status==='success'){ location.reload(); } else { alert(d.message||'Gagal melamar'); } });
}
</script>
@endpush
@endsection
