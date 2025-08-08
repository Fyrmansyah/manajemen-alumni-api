@extends('layouts.app')

@section('title', 'CV Saya')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="fas fa-file-alt text-primary me-2"></i>
                    CV Saya
                </h2>
                <a href="{{ route('alumni.cv.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    Buat CV Baru
                </a>
            </div>

            @if($cvs->count() > 0)
                <div class="row">
                    @foreach($cvs as $cv)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 shadow-sm border-0">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h5 class="card-title mb-0 text-primary">{{ $cv->title }}</h5>
                                        @if($cv->is_default)
                                            <span class="badge bg-success">
                                                <i class="fas fa-star me-1"></i>
                                                Default
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <i class="fas fa-palette me-1"></i>
                                            Template: {{ ucfirst($cv->template) }}
                                        </small>
                                    </div>

                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            Dibuat: {{ $cv->created_at->format('d M Y H:i') }}
                                        </small>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <a href="{{ route('alumni.cv.show', $cv->id) }}" 
                                           class="btn btn-outline-primary btn-sm flex-fill">
                                            <i class="fas fa-eye me-1"></i>
                                            Lihat
                                        </a>
                                        <a href="{{ route('alumni.cv.download', $cv->id) }}" 
                                           class="btn btn-outline-success btn-sm flex-fill">
                                            <i class="fas fa-download me-1"></i>
                                            Download
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="card-footer bg-transparent border-top-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <button type="button" 
                                                class="btn btn-outline-warning btn-sm"
                                                onclick="setAsDefault({{ $cv->id }})"
                                                {{ $cv->is_default ? 'disabled' : '' }}>
                                            <i class="fas fa-star me-1"></i>
                                            Set Default
                                        </button>
                                        
                                        <button type="button" 
                                                class="btn btn-outline-danger btn-sm"
                                                onclick="deleteCV({{ $cv->id }}, '{{ $cv->title }}')">
                                            <i class="fas fa-trash me-1"></i>
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-file-alt text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="text-muted mb-3">Belum ada CV</h4>
                    <p class="text-muted mb-4">
                        Buat CV pertama Anda untuk melamar pekerjaan dengan lebih mudah.
                    </p>
                    <a href="{{ route('alumni.cv.create') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus me-2"></i>
                        Buat CV Pertama
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                    Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus CV "<span id="cvTitle"></span>"?</p>
                <p class="text-muted small">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>
                    Batal
                </button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Set Default Confirmation Modal -->
<div class="modal fade" id="defaultModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-star text-warning me-2"></i>
                    Set CV Default
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin mengatur CV ini sebagai default?</p>
                <p class="text-muted small">CV default akan digunakan secara otomatis saat melamar pekerjaan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>
                    Batal
                </button>
                <form id="defaultForm" method="POST" style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-star me-1"></i>
                        Set Default
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function deleteCV(cvId, cvTitle) {
    document.getElementById('cvTitle').textContent = cvTitle;
    document.getElementById('deleteForm').action = `/alumni/cv/${cvId}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

function setAsDefault(cvId) {
    document.getElementById('defaultForm').action = `/alumni/cv/${cvId}/default`;
    new bootstrap.Modal(document.getElementById('defaultModal')).show();
}

// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});
</script>
@endpush 