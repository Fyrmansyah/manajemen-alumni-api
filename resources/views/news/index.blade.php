@extends('layouts.app')

@section('title', 'Berita & Pengumuman - BKK SMKN 1 Surabaya')

@section('content')
<div class="container mt-4 mb-5">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-primary text-white p-4 rounded">
                <h1 class="h2 mb-2">
                    <i class="fas fa-newspaper me-2"></i>Berita & Pengumuman
                </h1>
                <p class="mb-0">Dapatkan informasi terbaru seputar dunia kerja dan kegiatan BKK SMKN 1 Surabaya</p>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-filter me-2"></i>Filter Berita
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('news.index') }}" id="filterForm">
                        <!-- Search -->
                        <div class="mb-3">
                            <label for="search" class="form-label">Cari Berita</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="search" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Kata kunci...">
                        </div>

                        <!-- Category -->
                        <div class="mb-3">
                            <label for="category" class="form-label">Kategori</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $key => $value)
                                    <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date Range -->
                        <div class="mb-3">
                            <label for="date_from" class="form-label">Dari Tanggal</label>
                            <input type="date" 
                                   class="form-control" 
                                   id="date_from" 
                                   name="date_from" 
                                   value="{{ request('date_from') }}">
                        </div>

                        <div class="mb-3">
                            <label for="date_to" class="form-label">Sampai Tanggal</label>
                            <input type="date" 
                                   class="form-control" 
                                   id="date_to" 
                                   name="date_to" 
                                   value="{{ request('date_to') }}">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Cari
                            </button>
                            <a href="{{ route('news.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Popular News -->
            @if($popularNews->count() > 0)
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-fire me-2"></i>Berita Populer
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($popularNews as $popular)
                    <div class="d-flex mb-3 {{ !$loop->last ? 'border-bottom pb-3' : '' }}">
                        @if($popular->featured_image)
                            <img src="{{ asset('storage/' . $popular->featured_image) }}" 
                                 alt="{{ $popular->title }}" 
                                 class="rounded me-3" 
                                 style="width: 60px; height: 60px; object-fit: cover;">
                        @else
                            <div class="bg-secondary rounded me-3 d-flex align-items-center justify-content-center" 
                                 style="width: 60px; height: 60px;">
                                <i class="fas fa-newspaper text-white"></i>
                            </div>
                        @endif
                        <div class="flex-grow-1">
                            <h6 class="mb-1">
                                <a href="{{ route('news.show', $popular->id) }}" class="text-decoration-none">
                                    {{ Str::limit($popular->title, 60) }}
                                </a>
                            </h6>
                            <small class="text-muted">{{ $popular->created_at->format('d M Y') }}</small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- News List -->
        <div class="col-lg-9">
            <!-- Results Summary -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <p class="text-muted mb-0">
                    Menampilkan {{ $news->firstItem() ?? 0 }}-{{ $news->lastItem() ?? 0 }} dari {{ $news->total() }} berita
                </p>
                <div class="d-flex align-items-center">
                    <label for="sort" class="form-label me-2 mb-0">Urutkan:</label>
                    <select class="form-select form-select-sm" id="sort" name="sort" style="width: auto;" onchange="document.getElementById('filterForm').submit();">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                        <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Terpopuler</option>
                    </select>
                </div>
            </div>

            <!-- Featured News -->
            @if($featuredNews && request()->get('page', 1) == 1)
            <div class="card shadow-sm mb-4 featured-news">
                <div class="row g-0">
                    @if($featuredNews->featured_image)
                    <div class="col-md-5">
                        <img src="{{ asset('storage/' . $featuredNews->featured_image) }}" 
                             alt="{{ $featuredNews->title }}" 
                             class="img-fluid rounded-start h-100" 
                             style="object-fit: cover;">
                    </div>
                    @endif
                    <div class="col-md-{{ $featuredNews->featured_image ? '7' : '12' }}">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-star me-1"></i>Unggulan
                                </span>
                                <small class="text-muted">{{ $featuredNews->created_at->format('d M Y') }}</small>
                            </div>
                            <h3 class="card-title">
                                <a href="{{ route('news.show', $featuredNews->id) }}" class="text-decoration-none">
                                    {{ $featuredNews->title }}
                                </a>
                            </h3>
                            <p class="card-text text-muted mb-3">
                                @if($featuredNews->excerpt)
                                    {{ $featuredNews->excerpt }}
                                @else
                                    {{ Str::limit(strip_tags($featuredNews->content), 200) }}
                                @endif
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted">
                                        <i class="fas fa-eye me-1"></i>{{ $featuredNews->views ?? 0 }} views
                                    </small>
                                </div>
                                <a href="{{ route('news.show', $featuredNews->id) }}" class="btn btn-outline-primary">
                                    Baca Selengkapnya
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- News Cards -->
            <div class="row">
                @forelse($news as $article)
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm h-100 news-card">
                            @if($article->featured_image)
                                <img src="{{ asset('storage/' . $article->featured_image) }}" 
                                     alt="{{ $article->title }}" 
                                     class="card-img-top" 
                                     style="height: 200px; object-fit: cover;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center" 
                                     style="height: 200px;">
                                    <i class="fas fa-newspaper fa-3x text-muted"></i>
                                </div>
                            @endif
                            
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <small class="text-muted">{{ $article->created_at->format('d M Y') }}</small>
                                </div>
                                
                                <h5 class="card-title">
                                    <a href="{{ route('news.show', $article->id) }}" class="text-decoration-none">
                                        {{ $article->title }}
                                    </a>
                                </h5>
                                
                                <p class="card-text text-muted flex-grow-1">
                                    @if($article->excerpt)
                                        {{ Str::limit($article->excerpt, 120) }}
                                    @else
                                        {{ Str::limit(strip_tags($article->content), 120) }}
                                    @endif
                                </p>
                                
                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                    <small class="text-muted">
                                        <i class="fas fa-eye me-1"></i>{{ $article->views ?? 0 }} views
                                    </small>
                                    <a href="{{ route('news.show', $article->id) }}" class="btn btn-outline-primary btn-sm">
                                        Baca Selengkapnya
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">Tidak ada berita ditemukan</h4>
                            <p class="text-muted">Coba ubah kriteria pencarian Anda</p>
                            <a href="{{ route('news.index') }}" class="btn btn-primary">
                                <i class="fas fa-refresh me-2"></i>Lihat Semua Berita
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($news->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $news->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-submit form when sort changes
document.getElementById('sort').addEventListener('change', function() {
    // Add sort parameter to filter form
    const filterForm = document.getElementById('filterForm');
    const sortInput = document.createElement('input');
    sortInput.type = 'hidden';
    sortInput.name = 'sort';
    sortInput.value = this.value;
    filterForm.appendChild(sortInput);
    filterForm.submit();
});

// Auto-submit form when filters change
document.querySelectorAll('#filterForm select, #filterForm input[type="date"]').forEach(element => {
    element.addEventListener('change', function() {
        if (this.id !== 'sort') {
            document.getElementById('filterForm').submit();
        }
    });
});
</script>
@endpush

@section('styles')
<style>
.news-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.news-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.featured-news {
    border-left: 4px solid #ffc107;
}

.card-title a:hover {
    text-decoration: underline !important;
}

.form-select-sm {
    font-size: 0.875rem;
}
</style>
@endsection
