@extends('layouts.app')

@section('title', $cv->title)

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-0">
                        <i class="fas fa-file-alt text-primary me-2"></i>
                        {{ $cv->title }}
                    </h2>
                    <small class="text-muted">
                        Template: {{ ucfirst($cv->template) }} | 
                        Dibuat: {{ $cv->created_at->format('d M Y H:i') }}
                        @if($cv->is_default)
                            | <span class="badge bg-success">Default</span>
                        @endif
                    </small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('alumni.cv.download', $cv->id) }}" class="btn btn-success">
                        <i class="fas fa-download me-1"></i>
                        Download PDF
                    </a>
                    <a href="{{ route('alumni.cv.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Kembali
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-eye me-2"></i>
                            Preview CV
                        </h5>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-secondary" onclick="openFullscreen()">
                                <i class="fas fa-expand"></i> Full Screen
                            </button>
                            <a href="{{ route('alumni.cv.preview', $cv->id) }}" target="_blank" class="btn btn-outline-primary">
                                <i class="fas fa-external-link-alt"></i> Buka Tab Baru
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div style="height: 700px; background: #f8f9fa;">
                        <iframe id="cvPreview"
                                src="{{ route('alumni.cv.preview', $cv->id) }}" 
                                frameborder="0" 
                                style="width: 100%; height: 100%; border: none;"
                                onerror="handleIframeError()">
                            <p>Browser Anda tidak mendukung iframe. 
                               <a href="{{ route('alumni.cv.preview', $cv->id) }}" target="_blank">
                                   Klik di sini untuk melihat CV
                               </a>
                            </p>
                        </iframe>
                        
                        <!-- Error fallback (hidden by default) -->
                        <div id="previewError" style="display: none; height: 100%; padding: 50px; text-align: center;">
                            <div class="text-center">
                                <i class="fas fa-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                                <h5 class="mt-3 text-muted">Tidak Dapat Memuat Preview</h5>
                                <p class="text-muted">File CV mungkin tidak tersedia atau format tidak didukung.</p>
                                <div class="mt-3">
                                    <a href="{{ route('alumni.cv.preview', $cv->id) }}" target="_blank" class="btn btn-primary me-2">
                                        <i class="fas fa-external-link-alt me-1"></i>
                                        Buka di Tab Baru
                                    </a>
                                    <a href="{{ route('alumni.cv.download', $cv->id) }}" class="btn btn-success">
                                        <i class="fas fa-download me-1"></i>
                                        Download PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                Informasi CV
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <strong>Judul:</strong>
                                </div>
                                <div class="col-6">
                                    {{ $cv->title }}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-6">
                                    <strong>Template:</strong>
                                </div>
                                <div class="col-6">
                                    {{ ucfirst($cv->template) }}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-6">
                                    <strong>Status:</strong>
                                </div>
                                <div class="col-6">
                                    @if($cv->is_default)
                                        <span class="badge bg-success">Default</span>
                                    @else
                                        <span class="badge bg-secondary">Biasa</span>
                                    @endif
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-6">
                                    <strong>Dibuat:</strong>
                                </div>
                                <div class="col-6">
                                    {{ $cv->created_at->format('d M Y H:i') }}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-6">
                                    <strong>Terakhir Update:</strong>
                                </div>
                                <div class="col-6">
                                    {{ $cv->updated_at->format('d M Y H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0">
                                <i class="fas fa-cog me-2"></i>
                                Aksi
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('alumni.cv.download', $cv->id) }}" 
                                   class="btn btn-success">
                                    <i class="fas fa-download me-2"></i>
                                    Download PDF
                                </a>
                                
                                @if(!$cv->is_default)
                                    <button type="button" 
                                            class="btn btn-warning"
                                            onclick="setAsDefault({{ $cv->id }})">
                                        <i class="fas fa-star me-2"></i>
                                        Set sebagai Default
                                    </button>
                                @endif
                                
                                <button type="button" 
                                        class="btn btn-danger"
                                        onclick="deleteCV({{ $cv->id }}, '{{ $cv->title }}')">
                                    <i class="fas fa-trash me-2"></i>
                                    Hapus CV
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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

function openFullscreen() {
    const iframe = document.getElementById('cvPreview');
    const cvUrl = iframe.src;
    
    // Buka CV di window baru dengan ukuran penuh
    const newWindow = window.open(cvUrl, '_blank', 'width=' + screen.width + ',height=' + screen.height + ',fullscreen=yes');
    if (newWindow) {
        newWindow.focus();
    }
}

function handleIframeError() {
    console.log('Iframe error detected');
    document.getElementById('cvPreview').style.display = 'none';
    document.getElementById('previewError').style.display = 'block';
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
    
    // Tambahkan loading indicator untuk iframe
    const iframe = document.getElementById('cvPreview');
    const loadingHtml = `
        <div class="d-flex justify-content-center align-items-center" style="height: 100%; background: #f8f9fa;">
            <div class="text-center">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="text-muted">Memuat preview CV...</p>
            </div>
        </div>
    `;
    
    const loadingDiv = document.createElement('div');
    loadingDiv.innerHTML = loadingHtml;
    loadingDiv.style.cssText = 'position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 10;';
    iframe.parentNode.style.position = 'relative';
    iframe.parentNode.appendChild(loadingDiv);
    
    iframe.onload = function() {
        loadingDiv.remove();
        
        // Check if iframe content loaded successfully
        try {
            // Coba akses iframe content untuk detect error
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            if (iframeDoc.body && iframeDoc.body.innerHTML.includes('404') || 
                iframeDoc.body.innerHTML.includes('error') ||
                iframeDoc.body.innerHTML.includes('not found')) {
                handleIframeError();
            }
        } catch (e) {
            // Cross-origin atau error lain, ignore
            console.log('Cannot access iframe content (normal for PDF files)');
        }
    };
    
    iframe.onerror = function() {
        loadingDiv.remove();
        handleIframeError();
    };
    
    // Timeout fallback
    setTimeout(function() {
        if (loadingDiv.parentNode) {
            loadingDiv.remove();
        }
    }, 10000); // 10 second timeout
});
</script>
@endpush 