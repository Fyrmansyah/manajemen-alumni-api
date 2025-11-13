@extends('layouts.app')

@section('title', $news->title . ' - Admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-eye text-primary me-2"></i>
                        Detail Berita
                    </h1>
                    <p class="text-muted mb-0">Tampilan detail berita untuk admin</p>
                </div>
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.news.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Kembali
                    </a>
                    <a href="{{ route('admin.news.edit', $news) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i>
                        Edit
                    </a>
                    <form action="{{ route('admin.news.destroy', $news) }}" method="POST" 
                          class="d-inline" onsubmit="return confirm('Yakin ingin menghapus berita ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i>
                            Hapus
                        </button>
                    </form>
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
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <!-- News Content -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <!-- Featured Image -->
                            @if($news->featured_image)
                                <div class="text-center mb-4">
                                    <img src="{{ asset('storage/' . $news->featured_image) }}" 
                                         alt="{{ $news->title }}" 
                                         class="img-fluid rounded" 
                                         style="max-height: 400px; width: auto;">
                                </div>
                            @endif

                            <!-- Title -->
                            <h1 class="mb-3">{{ $news->title }}</h1>

                            <!-- Meta Info -->
                            <div class="d-flex flex-wrap gap-3 mb-4 pb-3 border-bottom">
                                <div class="d-flex align-items-center text-muted">
                                    <i class="fas fa-calendar me-2"></i>
                                    <span>{{ $news->created_at->format('d F Y, H:i') }} WIB</span>
                                </div>
                                
                                @if($news->published_at)
                                <div class="d-flex align-items-center text-muted">
                                    <i class="fas fa-globe me-2"></i>
                                    <span>Dipublikasi: {{ $news->published_at->format('d F Y, H:i') }} WIB</span>
                                </div>
                                @endif
                                
                                <div class="d-flex align-items-center text-muted">
                                    <i class="fas fa-eye me-2"></i>
                                    <span>{{ number_format($news->views ?? 0) }} dilihat</span>
                                </div>
                                
                                @if($news->author)
                                <div class="d-flex align-items-center text-muted">
                                    <i class="fas fa-user me-2"></i>
                                    <span>{{ $news->author->name ?? $news->author->username }}</span>
                                </div>
                                @endif
                            </div>

                            

                            <!-- Content -->
                            <div class="content">
                                <div class="admin-content-preview">
                                    {!! $news->content !!}
                                </div>
                            </div>

                            <!-- Tags -->
                            @if($news->tags)
                                <div class="mt-4 pt-3 border-top">
                                    <h6 class="text-muted mb-2">Tags:</h6>
                                    @foreach(explode(',', $news->tags) as $tag)
                                        <span class="badge bg-secondary me-1">#{{ trim($tag) }}</span>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Meta Description -->
                            @if($news->meta_description)
                                <div class="mt-3">
                                    <small class="text-muted">
                                        <strong>Meta Description:</strong> {{ $news->meta_description }}
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- News Info -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                Informasi Berita
                            </h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="text-muted"><strong>ID:</strong></td>
                                    <td>{{ $news->id }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><strong>Slug:</strong></td>
                                    <td>
                                        <code>{{ $news->slug }}</code>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><strong>Kategori:</strong></td>
                                    <td>
                                        @switch($news->category)
                                            @case('info')
                                                <span class="badge bg-info">Informasi</span>
                                                @break
                                            @case('job')
                                                <span class="badge bg-success">Lowongan Kerja</span>
                                                @break
                                            @case('event')
                                                <span class="badge bg-warning text-dark">Kegiatan</span>
                                                @break
                                            @case('announcement')
                                                <span class="badge bg-danger">Pengumuman</span>
                                                @break
                                            @case('achievement')
                                                <span class="badge bg-purple">Prestasi</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ ucfirst($news->category) }}</span>
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><strong>Status:</strong></td>
                                    <td>
                                        @switch($news->status)
                                            @case('published')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-eye me-1"></i>Terbitkan
                                                </span>
                                                @break
                                            @case('draft')
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-edit me-1"></i>Draft
                                                </span>
                                                @break
                                            @case('archived')
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-archive me-1"></i>Arsip
                                                </span>
                                                @break
                                            @default
                                                <span class="badge bg-light text-dark">{{ ucfirst($news->status) }}</span>
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><strong>Dibuat:</strong></td>
                                    <td>{{ $news->created_at->format('d M Y, H:i') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><strong>Diperbarui:</strong></td>
                                    <td>{{ $news->updated_at->format('d M Y, H:i') }}</td>
                                </tr>
                                @if($news->published_at)
                                <tr>
                                    <td class="text-muted"><strong>Dipublikasi:</strong></td>
                                    <td>{{ $news->published_at->format('d M Y, H:i') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <!-- SEO Info -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-search me-2"></i>
                                SEO & Statistik
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="border-end">
                                        <h4 class="text-primary mb-1">{{ number_format($news->views ?? 0) }}</h4>
                                        <small class="text-muted">Total Views</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h4 class="text-success mb-1">{{ str_word_count(strip_tags($news->content)) }}</h4>
                                    <small class="text-muted">Kata</small>
                                </div>
                            </div>
                            
                            @if($news->meta_description)
                                <div class="mt-3 pt-3 border-top">
                                    <small class="text-muted"><strong>Meta Description:</strong></small>
                                    <p class="small">{{ $news->meta_description }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0">
                                <i class="fas fa-bolt me-2"></i>
                                Aksi Cepat
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                @if($news->status === 'draft')
                                    <form action="{{ route('admin.news.update', $news) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="title" value="{{ $news->title }}">
                                        <input type="hidden" name="content" value="{{ $news->content }}">
                                        <input type="hidden" name="category" value="{{ $news->category }}">
                                        <input type="hidden" name="status" value="published">
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="fas fa-paper-plane me-2"></i>
                                            Publikasikan Sekarang
                                        </button>
                                    </form>
                                @elseif($news->status === 'published')
                                    <form action="{{ route('admin.news.update', $news) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="title" value="{{ $news->title }}">
                                        <input type="hidden" name="content" value="{{ $news->content }}">
                                        <input type="hidden" name="category" value="{{ $news->category }}">
                                        <input type="hidden" name="status" value="archived">
                                        <button type="submit" class="btn btn-secondary w-100">
                                            <i class="fas fa-archive me-2"></i>
                                            Arsipkan
                                        </button>
                                    </form>
                                @endif
                                
                                <a href="{{ route('admin.news.edit', $news) }}" class="btn btn-outline-warning w-100">
                                    <i class="fas fa-edit me-2"></i>
                                    Edit Berita
                                </a>
                                
                                <button class="btn btn-outline-info w-100" onclick="copyToClipboard('{{ url('/news/' . $news->slug) }}')">
                                    <i class="fas fa-link me-2"></i>
                                    Salin Link Publik
                                </button>

                                <div class="dropdown-divider"></div>
                                
                                <form action="{{ route('admin.news.destroy', $news) }}" method="POST" 
                                      onsubmit="return confirm('Yakin ingin menghapus berita ini secara permanen?')" class="d-grid">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        <i class="fas fa-trash me-2"></i>
                                        Hapus Berita
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .bg-purple {
        background-color: #6f42c1 !important;
    }
    
    .content {
        line-height: 1.8;
        font-size: 1.1rem;
    }
    
    .content img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin: 1rem 0;
    }
</style>
@endpush

@push('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show success message
        const toast = document.createElement('div');
        toast.className = 'toast align-items-center text-white bg-success border-0 position-fixed';
        toast.style.top = '20px';
        toast.style.right = '20px';
        toast.style.zIndex = '9999';
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-check me-2"></i>Link berhasil disalin!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        document.body.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
        bsToast.show();
        
        toast.addEventListener('hidden.bs.toast', function() {
            document.body.removeChild(toast);
        });
    }).catch(function(err) {
        alert('Gagal menyalin link: ' + err);
    });
}
</script>

@push('styles')
<style>
.admin-content-preview {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 2rem;
    line-height: 1.7;
    font-size: 1rem;
}

.admin-content-preview p {
    margin-bottom: 1.2rem;
    text-align: justify;
}

.admin-content-preview a {
    color: #0066cc;
    text-decoration: underline;
}

.admin-content-preview a:hover {
    color: #0052a3;
}

.admin-content-preview img {
    max-width: 100%;
    height: auto;
    border-radius: 6px;
    margin: 1rem 0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.admin-content-preview blockquote {
    border-left: 4px solid #007bff;
    padding-left: 1rem;
    margin: 1rem 0;
    font-style: italic;
    color: #666;
}

.admin-content-preview h1,
.admin-content-preview h2,
.admin-content-preview h3,
.admin-content-preview h4,
.admin-content-preview h5,
.admin-content-preview h6 {
    color: #333;
    margin-top: 1.5rem;
    margin-bottom: 1rem;
}
</style>
@endpush
@endpush
