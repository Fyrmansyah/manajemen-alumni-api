@extends('layouts.app')

@section('title', 'Kelola Berita - Admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-newspaper text-primary me-2"></i>
                        Kelola Berita
                    </h1>
                    <p class="text-muted mb-0">Kelola berita dan pengumuman untuk alumni dan perusahaan</p>
                </div>
                <a href="{{ route('admin.news.create') }}" class="btn btn-success">
                    <i class="fas fa-plus me-1"></i>
                    Buat Berita Baru
                </a>
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

            <!-- Filters -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.news.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Pencarian</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Cari berdasarkan judul atau konten...">
                        </div>
                        <div class="col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Semua Status</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Terbitkan</option>
                                <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Arsip</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="category" class="form-label">Kategori</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">Semua Kategori</option>
                                <option value="info" {{ request('category') == 'info' ? 'selected' : '' }}>Informasi</option>
                                <option value="job" {{ request('category') == 'job' ? 'selected' : '' }}>Lowongan Kerja</option>
                                <option value="event" {{ request('category') == 'event' ? 'selected' : '' }}>Kegiatan</option>
                                <option value="announcement" {{ request('category') == 'announcement' ? 'selected' : '' }}>Pengumuman</option>
                                <option value="achievement" {{ request('category') == 'achievement' ? 'selected' : '' }}>Prestasi</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="sort" class="form-label">Urutkan</label>
                            <select class="form-select" id="sort" name="sort">
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                                <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>Judul A-Z</option>
                                <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>Judul Z-A</option>
                                <option value="views" {{ request('sort') == 'views' ? 'selected' : '' }}>Paling Dilihat</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="d-grid gap-2 w-100">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- News Table -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            Daftar Berita ({{ $news->total() }} total)
                        </h5>
                        @if(request()->hasAny(['search', 'status', 'category', 'sort']))
                            <a href="{{ route('admin.news.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-times me-1"></i>Reset Filter
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($news->count() > 0)
                        <div class="table-responsive-md">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 45%;">Judul</th>
                                        <th style="width: 10%;">Kategori</th>
                                        <th style="width: 10%;">Status</th>
                                        <th style="width: 8%;">Views</th>
                                        <th style="width: 12%;">Dibuat</th>
                                        <th style="width: 15%;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($news as $item)
                                        <tr>
                                            <td style="max-width: 0; overflow: hidden;">
                                                <div class="d-flex align-items-center">
                                                    @if($item->featured_image)
                                                        <img src="{{ asset('storage/' . $item->featured_image) }}" 
                                                             alt="{{ $item->title }}" 
                                                             class="rounded me-2 flex-shrink-0" 
                                                             style="width: 45px; height: 30px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-secondary rounded me-2 d-flex align-items-center justify-content-center flex-shrink-0" 
                                                             style="width: 45px; height: 30px;">
                                                            <i class="fas fa-newspaper text-white small"></i>
                                                        </div>
                                                    @endif
                                                    <div class="min-w-0 flex-grow-1">
                                                        <h6 class="mb-0 text-truncate" title="{{ $item->title }}">{{ $item->title }}</h6>
                                                        <small class="text-muted text-truncate d-block" title="{{ strip_tags($item->content) }}">
                                                            {{ Str::limit(strip_tags($item->content), 60) }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @switch($item->category)
                                                    @case('info')
                                                        <span class="badge bg-info">Informasi</span>
                                                        @break
                                                    @case('job')
                                                        <span class="badge bg-success">Lowongan</span>
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
                                                        <span class="badge bg-secondary">{{ ucfirst($item->category) }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                @switch($item->status)
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
                                                        <span class="badge bg-light text-dark">{{ ucfirst($item->status) }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    <i class="fas fa-eye me-1"></i>{{ number_format($item->views ?? 0) }}
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $item->created_at->format('d M Y') }}<br>
                                                    {{ $item->created_at->format('H:i') }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.news.show', $item) }}" 
                                                       class="btn btn-sm btn-outline-info" 
                                                       title="Lihat">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.news.edit', $item) }}" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.news.destroy', $item) }}" method="POST" 
                                                          onsubmit="return confirm('Yakin ingin menghapus berita ini?')" 
                                                          class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-newspaper text-muted" style="font-size: 4rem;"></i>
                            <h5 class="text-muted mt-3">Belum Ada Berita</h5>
                            <p class="text-muted">Mulai dengan membuat berita pertama Anda</p>
                            <a href="{{ route('admin.news.create') }}" class="btn btn-success">
                                <i class="fas fa-plus me-1"></i>Buat Berita Baru
                            </a>
                        </div>
                    @endif
                </div>

                @if($news->hasPages())
                    <div class="card-footer bg-white border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                Menampilkan {{ $news->firstItem() }} - {{ $news->lastItem() }} dari {{ $news->total() }} berita
                            </div>
                            {{ $news->links() }}
                        </div>
                    </div>
                @endif
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
    
    .table td {
        vertical-align: middle;
    }
    
    .dropdown-toggle::after {
        display: none;
    }
</style>
@endpush

@push('styles')
<style>
    /* Prevent table from overflowing and creating horizontal scroll */
    .table-responsive-md {
        overflow-x: auto;
    }
    
    @media (min-width: 768px) {
        .table-responsive-md {
            overflow-x: visible;
        }
    }
    
    /* Ensure action buttons are always visible */
    .btn-group .btn {
        white-space: nowrap;
    }
    
    /* Fix text overflow in title column */
    .text-truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .min-w-0 {
        min-width: 0;
    }
    
    /* Ensure table cells don't wrap and maintain layout */
    .table td {
        vertical-align: middle;
        white-space: nowrap;
    }
    
    .table td:first-child {
        white-space: normal; /* Allow title column to wrap */
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when filter changes
    const filters = document.querySelectorAll('#status, #category, #sort');
    filters.forEach(filter => {
        filter.addEventListener('change', function() {
            this.form.submit();
        });
    });
    
    // Search input auto-submit on Enter
    const searchInput = document.getElementById('search');
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            this.form.submit();
        }
    });
});
</script>
@endpush
