@extends('layouts.app')

@section('title', 'Detail Lowongan Kerja - Admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-briefcase text-primary me-2"></i>
                        Detail Lowongan Kerja
                    </h1>
                    <p class="text-muted mb-0">{{ $job->title }} - {{ $job->company->company_name }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.jobs.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Kembali
                    </a>
                    @if(!$job->isArchived())
                        <a href="{{ route('admin.jobs.edit', $job) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i>
                            Edit
                        </a>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#archiveModal">
                            <i class="fas fa-archive me-1"></i>
                            Arsipkan
                        </button>
                    @else
                        <form action="{{ route('admin.jobs.reactivate', $job) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success" onclick="return confirm('Yakin ingin mengaktifkan kembali lowongan ini?')">
                                <i class="fas fa-undo me-1"></i>
                                Aktifkan Kembali
                            </button>
                        </form>
                    @endif
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

            <div class="row">
                <!-- Job Details -->
                <div class="col-lg-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-info-circle text-primary me-2"></i>
                                Informasi Lowongan
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Judul Lowongan:</strong>
                                    <p class="mb-2">{{ $job->title }}</p>
                                </div>
                                <div class="col-md-6">
                                    <strong>Perusahaan:</strong>
                                    <p class="mb-2">{{ $job->company->company_name }}</p>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Tipe Pekerjaan:</strong>
                                    <p class="mb-2">
                                        <span>{{ App\Models\Job::JOB_TYPES[$job->type] ?? $job->type }}</span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <strong>Lokasi:</strong>
                                    <p class="mb-2">{{ $job->location }}</p>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Status:</strong>
                                    <p class="mb-2">
                                        @if($job->isArchived())
                                            <span>Diarsipkan</span>
                                        @else
                                            @switch($job->status)
                                                @case('active')
                                                    <span>Aktif</span>
                                                    @break
                                                @case('draft')
                                                    <span>Draft</span>
                                                    @break
                                                @case('closed')
                                                    <span>Ditutup</span>
                                                    @break
                                                @default
                                                    <span>{{ $job->status }}</span>
                                            @endswitch
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <strong>Posisi Tersedia:</strong>
                                    <p class="mb-2">{{ $job->positions_available }} posisi</p>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Gaji:</strong>
                                    <p class="mb-2">{{ $job->formatted_salary }}</p>
                                </div>
                                <div class="col-md-6">
                                    <strong>Batas Lamaran:</strong>
                                    <p class="mb-2">
                                        {{ $job->application_deadline->format('d M Y') }}
                                        @php $deadlineInfo = $job->getDeadlineInfo(); @endphp
                                        @if($deadlineInfo)
                                            <br><small class="{{ $deadlineInfo['css_class'] }}">
                                                {{ $deadlineInfo['text'] }}
                                            </small>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="mb-3">
                                <strong>Deskripsi Pekerjaan:</strong>
                                <div class="mt-2 p-3 bg-light rounded">
                                    {!! nl2br(e($job->description)) !!}
                                </div>
                            </div>

                            <div class="mb-3">
                                <strong>Persyaratan:</strong>
                                <div class="mt-2 p-3 bg-light rounded">
                                    {!! nl2br(e($job->requirements)) !!}
                                </div>
                            </div>

                            @if($job->isArchived())
                                <div class="mb-3">
                                    <strong>Informasi Arsip:</strong>
                                    <div class="mt-2 p-3 bg-warning bg-opacity-10 border border-warning rounded">
                                        <small class="text-muted">
                                            <i class="fas fa-archive me-1"></i>
                                            Diarsipkan pada: {{ $job->archived_at->format('d M Y H:i') }}<br>
                                            Alasan: {{ $job->archive_reason ?? 'Tidak ada alasan' }}
                                        </small>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Applications & Stats -->
                <div class="col-lg-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-chart-bar text-primary me-2"></i>
                                Statistik Lamaran
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center">
                                <h3 class="text-primary">{{ $job->applications_count ?? $job->applications->count() }}</h3>
                                <p class="text-muted">Total Lamaran</p>
                            </div>
                            
                            @if($job->applications->count() > 0)
                                <div class="border-top pt-3">
                                    <strong>Pelamar Terbaru:</strong>
                                    <div class="mt-2">
                                        @foreach($job->applications->take(5) as $application)
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="flex-grow-1">
                                                    <small class="fw-medium">{{ $application->alumni->full_name ?? 'Alumni' }}</small><br>
                                                    <small class="text-muted">{{ $application->created_at->format('d M Y') }}</small>
                                                </div>
                                                <span>Baru</span>
                                            </div>
                                        @endforeach
                                        
                                        @if($job->applications->count() > 5)
                                            <small class="text-muted">
                                                dan {{ $job->applications->count() - 5 }} pelamar lainnya...
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-tools text-primary me-2"></i>
                                Aksi Cepat
                            </h5>
                        </div>
                        <div class="card-body">
                            @if(!$job->isArchived())
                                <div class="d-grid gap-2">
                                    <a href="{{ route('admin.jobs.edit', $job) }}" class="btn btn-outline-warning">
                                        <i class="fas fa-edit me-1"></i>
                                        Edit Lowongan
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#archiveModal">
                                        <i class="fas fa-archive me-1"></i>
                                        Arsipkan Lowongan
                                    </button>
                                    <form action="{{ route('admin.jobs.destroy', $job) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-dark w-100" onclick="return confirm('Yakin ingin menghapus lowongan ini? Tindakan ini tidak dapat dibatalkan!')">
                                            <i class="fas fa-trash me-1"></i>
                                            Hapus Lowongan
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="d-grid">
                                    <form action="{{ route('admin.jobs.reactivate', $job) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-outline-success" onclick="return confirm('Yakin ingin mengaktifkan kembali lowongan ini?')">
                                            <i class="fas fa-undo me-1"></i>
                                            Aktifkan Kembali
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Archive Modal -->
@if(!$job->isArchived())
<div class="modal fade" id="archiveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Arsipkan Lowongan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.jobs.archive', $job) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <p>Yakin ingin mengarsipkan lowongan "<strong>{{ $job->title }}</strong>"?</p>
                    <div class="mb-3">
                        <label for="reason" class="form-label">Alasan Arsip (opsional)</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" placeholder="Masukkan alasan mengapa lowongan ini diarsipkan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Arsipkan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
