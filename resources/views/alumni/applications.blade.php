@extends('layouts.app')

@section('title', 'Riwayat Lamaran - BKK SMKN 1 Surabaya')

@section('content')
<div class="container-fluid mt-4 mb-5">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-file-alt text-primary me-2"></i>
                        Riwayat Lamaran
                    </h1>
                    <p class="text-muted mb-0">Kelola dan pantau status lamaran pekerjaan Anda</p>
                </div>
                <div>
                    <a href="{{ route('alumni.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-3">
                    <form method="GET" action="{{ route('alumni.applications') }}" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="status" class="form-label small text-muted">Filter Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>
                                    <i class="fas fa-clock text-warning"></i> Submitted
                                </option>
                                <option value="reviewed" {{ request('status') == 'reviewed' ? 'selected' : '' }}>
                                    <i class="fas fa-eye text-info"></i> Reviewed
                                </option>
                                <option value="interview" {{ request('status') == 'interview' ? 'selected' : '' }}>
                                    <i class="fas fa-comments text-primary"></i> Interview
                                </option>
                                <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>
                                    <i class="fas fa-check-circle text-success"></i> Diterima
                                </option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>
                                    <i class="fas fa-times-circle text-danger"></i> Ditolak
                                </option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-1"></i>
                                Terapkan Filter
                            </button>
                            <a href="{{ route('alumni.applications') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-refresh me-1"></i>
                                Reset
                            </a>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Total: {{ $applications->total() }} lamaran
                            </small>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Applications List -->
    <div class="row">
        <div class="col-12">
            @if($applications->count() > 0)
                <div class="row">
                    @foreach($applications as $application)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-white border-bottom-0 pb-2">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <h6 class="card-title mb-1 text-truncate" title="{{ $application->job->title }}">
                                            {{ Str::limit($application->job->title, 30) }}
                                        </h6>
                                        @php
                                            $statusConfig = [
                                                'submitted' => ['class' => 'warning', 'icon' => 'clock', 'text' => 'Submitted'],
                                                'reviewed' => ['class' => 'info', 'icon' => 'eye', 'text' => 'Reviewed'],
                                                'interview' => ['class' => 'primary', 'icon' => 'comments', 'text' => 'Interview'],
                                                'accepted' => ['class' => 'success', 'icon' => 'check-circle', 'text' => 'Diterima'],
                                                'rejected' => ['class' => 'danger', 'icon' => 'times-circle', 'text' => 'Ditolak'],
                                            ];
                                            $status = $statusConfig[$application->status] ?? ['class' => 'secondary', 'icon' => 'question', 'text' => ucfirst($application->status)];
                                        @endphp
                                        <span class="badge bg-{{ $status['class'] }}">
                                            <i class="fas fa-{{ $status['icon'] }} me-1"></i>
                                            {{ $status['text'] }}
                                        </span>
                                    </div>
                                    <p class="text-muted small mb-0">
                                        <i class="fas fa-building me-1"></i>
                                        {{ $application->job->company->company_name ?? 'Nama perusahaan tidak tersedia' }}
                                    </p>
                                </div>
                                
                                <div class="card-body pt-2">
                                    <div class="mb-3">
                                        <small class="text-muted d-block">
                                            <i class="fas fa-calendar-alt me-1"></i>
                                            Dilamar: {{ $application->created_at->format('d M Y, H:i') }}
                                        </small>
                                        @if($application->reviewed_at)
                                            <small class="text-muted d-block">
                                                <i class="fas fa-calendar-check me-1"></i>
                                                Ditinjau: {{ $application->reviewed_at->format('d M Y, H:i') }}
                                            </small>
                                        @endif
                                    </div>

                                    @if($application->cover_letter)
                                        <div class="mb-3">
                                            <small class="text-muted d-block mb-1">Cover Letter:</small>
                                            <p class="small text-dark mb-0" style="max-height: 60px; overflow: hidden;">
                                                {{ Str::limit($application->cover_letter, 100) }}
                                            </p>
                                        </div>
                                    @endif

                                    @if($application->notes)
                                        <div class="mb-3">
                                            <small class="text-muted d-block mb-1">Catatan Perusahaan:</small>
                                            <p class="small text-primary mb-0" style="max-height: 40px; overflow: hidden;">
                                                {{ Str::limit($application->notes, 80) }}
                                            </p>
                                        </div>
                                    @endif

                                    <div class="d-flex gap-2 mt-3">
                                        <button type="button" class="btn btn-sm btn-outline-primary flex-fill"
                                                onclick="showApplicationDetail({{ $application->id }})">
                                            <i class="fas fa-eye me-1"></i>
                                            Detail
                                        </button>
                                        
                                        @if($application->cv_file)
                                            <a href="{{ asset('storage/cvs/' . $application->cv_file) }}" 
                                               target="_blank" class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-file-pdf me-1"></i>
                                                CV
                                            </a>
                                        @endif
                                        
                                        <a href="{{ route('jobs.show', $application->job->id) }}" 
                                           class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-external-link-alt me-1"></i>
                                            Job
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-center">
                            {{ $applications->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-file-alt text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="text-muted mb-3">Belum Ada Lamaran</h4>
                    <p class="text-muted mb-4">
                        Anda belum pernah mengirim lamaran pekerjaan. 
                        <br>Mulai jelajahi lowongan yang tersedia dan lamar sekarang!
                    </p>
                    <a href="{{ route('jobs.index') }}" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>
                        Cari Lowongan Kerja
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Application Detail Modal -->
<div class="modal fade" id="applicationDetailModal" tabindex="-1" aria-labelledby="applicationDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="applicationDetailModalLabel">
                    <i class="fas fa-file-alt me-2"></i>
                    Detail Lamaran
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="applicationDetailContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Memuat detail lamaran...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function showApplicationDetail(applicationId) {
    const modal = new bootstrap.Modal(document.getElementById('applicationDetailModal'));
    const modalContent = document.getElementById('applicationDetailContent');
    
    // Show loading state
    modalContent.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Memuat detail lamaran...</p>
        </div>
    `;
    
    modal.show();
    
    // Fetch application details (session-authenticated route)
    fetch(`/alumni/applications/${applicationId}`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
        .then(async response => {
            const contentType = response.headers.get('content-type') || '';
            const isJson = contentType.includes('application/json');
            const data = isJson ? await response.json() : null;
            if (!response.ok) {
                const msg = (data && data.message) ? data.message : (response.status === 401 ? 'Silakan login kembali' : 'Gagal memuat detail lamaran');
                throw new Error(msg);
            }
            return data;
        })
        .then(data => {
            if (data.success) {
                const app = data.data;
                const statusConfig = {
                    'submitted': { class: 'warning', icon: 'clock', text: 'Submitted' },
                    'reviewed': { class: 'info', icon: 'eye', text: 'Reviewed' },
                    'interview': { class: 'primary', icon: 'comments', text: 'Interview' },
                    'accepted': { class: 'success', icon: 'check-circle', text: 'Diterima' },
                    'rejected': { class: 'danger', icon: 'times-circle', text: 'Ditolak' }
                };
                const status = statusConfig[app.status] || { class: 'secondary', icon: 'question', text: app.status };
                
                modalContent.innerHTML = `
                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="fw-bold mb-3">${app.job.title}</h6>
                            <p class="text-muted mb-2">
                                <i class="fas fa-building me-2"></i>
                                ${app.job.company_name}
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <span class="badge bg-${status.class} fs-6">
                                <i class="fas fa-${status.icon} me-1"></i>
                                ${status.text}
                            </span>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <small class="text-muted d-block">Tanggal Lamaran</small>
                            <p class="mb-0">${app.applied_at}</p>
                        </div>
                        ${app.reviewed_at ? `
                        <div class="col-md-6">
                            <small class="text-muted d-block">Tanggal Ditinjau</small>
                            <p class="mb-0">${app.reviewed_at}</p>
                        </div>
                        ` : ''}
                    </div>
                    
                    ${app.cover_letter ? `
                    <div class="mb-3">
                        <h6 class="fw-bold">Cover Letter</h6>
                        <div class="p-3 bg-light rounded">
                            <p class="mb-0">${app.cover_letter}</p>
                        </div>
                    </div>
                    ` : ''}
                    
                    ${app.notes ? `
                    <div class="mb-3">
                        <h6 class="fw-bold">Catatan dari Perusahaan</h6>
                        <div class="p-3 bg-primary bg-opacity-10 rounded">
                            <p class="mb-0 text-primary">${app.notes}</p>
                        </div>
                    </div>
                    ` : ''}

                    ${(app.interview_at || app.interview_location || app.interview_details) ? `
                    <div class="mb-3">
                        <h6 class="fw-bold">Jadwal Interview</h6>
                        <div class="p-3 bg-success bg-opacity-10 rounded">
                            ${app.interview_at ? `<p class="mb-1"><i class=\"fas fa-calendar me-2\"></i>${app.interview_at}</p>` : ''}
                            ${app.interview_location ? `<p class="mb-1"><i class=\"fas fa-map-marker-alt me-2\"></i>${app.interview_location}</p>` : ''}
                            ${app.interview_details ? `<p class="mb-0"><i class=\"fas fa-info-circle me-2\"></i>${app.interview_details}</p>` : ''}
                        </div>
                    </div>
                    ` : ''}
                    
                    ${app.cv_path ? `
                    <div class="mb-3">
                        <h6 class="fw-bold">CV yang Dikirim</h6>
                        <a href="/storage/cvs/${app.cv_path}" target="_blank" class="btn btn-outline-success">
                            <i class="fas fa-file-pdf me-1"></i>
                            Lihat CV
                        </a>
                    </div>
                    ` : ''}
                `;
            } else {
                modalContent.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-exclamation-triangle text-warning mb-3" style="font-size: 2rem;"></i>
                        <p class="text-muted">Gagal memuat detail lamaran.</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            modalContent.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-exclamation-triangle text-danger mb-3" style="font-size: 2rem;"></i>
                    <p class="text-muted">${error.message || 'Terjadi kesalahan saat memuat data.'}</p>
                </div>
            `;
        });
}

// Auto-open detail if query parameter ?app={id} is present
document.addEventListener('DOMContentLoaded', function() {
    try {
        const params = new URLSearchParams(window.location.search);
        const appId = params.get('app');
        if (appId) {
            showApplicationDetail(appId);
        }
    } catch (e) {
        console.warn('Cannot parse URL params', e);
    }
});
</script>
@endpush

@push('styles')
<style>
.card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.badge {
    font-size: 0.75rem;
}

.btn-sm {
    font-size: 0.75rem;
}

@media (max-width: 768px) {
    .card-title {
        font-size: 0.9rem;
    }
    
    .btn-sm {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
    }
}
</style>
@endpush
