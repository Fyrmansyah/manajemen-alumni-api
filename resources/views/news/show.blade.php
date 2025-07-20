@extends('layouts.app')

@section('title', $news->title . ' - BKK SMKN 1 Surabaya')

@section('content')
<div class="container mt-4 mb-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('news.index') }}">Berita</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($news->title, 30) }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <article class="card shadow-sm mb-4">
                <div class="card-body p-4">
                    <!-- Article Header -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge bg-primary fs-6">{{ ucfirst($news->category) }}</span>
                            <div class="text-muted">
                                <small>
                                    <i class="fas fa-calendar me-1"></i>{{ $news->created_at->format('d F Y') }}
                                </small>
                                <small class="ms-3">
                                    <i class="fas fa-eye me-1"></i>{{ $news->views ?? 0 }} views
                                </small>
                            </div>
                        </div>
                        
                        <h1 class="h2 mb-3">{{ $news->title }}</h1>
                        
                        @if($news->image)
                            <div class="text-center mb-4">
                                <img src="{{ asset('storage/news_images/' . $news->image) }}" 
                                     alt="{{ $news->title }}" 
                                     class="img-fluid rounded shadow">
                            </div>
                        @endif
                    </div>

                    <!-- Article Content -->
                    <div class="article-content">
                        {!! nl2br(e($news->content)) !!}
                    </div>

                    <!-- Article Footer -->
                    <div class="border-top pt-4 mt-4">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <span class="text-muted me-3">Bagikan:</span>
                                    <div class="social-share">
                                        <a href="#" onclick="shareToFacebook()" class="btn btn-outline-primary btn-sm me-2">
                                            <i class="fab fa-facebook-f"></i>
                                        </a>
                                        <a href="#" onclick="shareToTwitter()" class="btn btn-outline-info btn-sm me-2">
                                            <i class="fab fa-twitter"></i>
                                        </a>
                                        <a href="#" onclick="shareToWhatsApp()" class="btn btn-outline-success btn-sm me-2">
                                            <i class="fab fa-whatsapp"></i>
                                        </a>
                                        <a href="#" onclick="copyLink()" class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-link"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                                <small class="text-muted">
                                    Dipublikasikan pada {{ $news->created_at->format('d F Y \p\u\k\u\l H:i') }} WIB
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </article>

            <!-- Navigation -->
            @if($previousNews || $nextNews)
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row">
                        @if($previousNews)
                        <div class="col-md-6 mb-3 mb-md-0">
                            <h6 class="text-muted mb-2">
                                <i class="fas fa-chevron-left me-1"></i>Berita Sebelumnya
                            </h6>
                            <a href="{{ route('news.show', $previousNews->id) }}" class="text-decoration-none">
                                <h6>{{ $previousNews->title }}</h6>
                            </a>
                        </div>
                        @endif
                        
                        @if($nextNews)
                        <div class="col-md-6 text-md-end">
                            <h6 class="text-muted mb-2">
                                Berita Selanjutnya<i class="fas fa-chevron-right ms-1"></i>
                            </h6>
                            <a href="{{ route('news.show', $nextNews->id) }}" class="text-decoration-none">
                                <h6>{{ $nextNews->title }}</h6>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Related News -->
            @if($relatedNews->count() > 0)
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Berita Terkait</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($relatedNews as $related)
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                @if($related->image)
                                    <img src="{{ asset('storage/news_images/' . $related->image) }}" 
                                         alt="{{ $related->title }}" 
                                         class="rounded me-3" 
                                         style="width: 80px; height: 80px; object-fit: cover;">
                                @else
                                    <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                         style="width: 80px; height: 80px;">
                                        <i class="fas fa-newspaper text-muted"></i>
                                    </div>
                                @endif
                                <div class="flex-grow-1">
                                    <h6>
                                        <a href="{{ route('news.show', $related->id) }}" class="text-decoration-none">
                                            {{ Str::limit($related->title, 60) }}
                                        </a>
                                    </h6>
                                    <small class="text-muted">
                                        {{ $related->created_at->format('d M Y') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Latest News -->
            @if($latestNews->count() > 0)
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-clock me-2"></i>Berita Terbaru
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($latestNews as $latest)
                    <div class="d-flex mb-3 {{ !$loop->last ? 'border-bottom pb-3' : '' }}">
                        @if($latest->image)
                            <img src="{{ asset('storage/news_images/' . $latest->image) }}" 
                                 alt="{{ $latest->title }}" 
                                 class="rounded me-3" 
                                 style="width: 60px; height: 60px; object-fit: cover;">
                        @else
                            <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                 style="width: 60px; height: 60px;">
                                <i class="fas fa-newspaper text-muted"></i>
                            </div>
                        @endif
                        <div class="flex-grow-1">
                            <h6 class="mb-1">
                                <a href="{{ route('news.show', $latest->id) }}" class="text-decoration-none">
                                    {{ Str::limit($latest->title, 60) }}
                                </a>
                            </h6>
                            <small class="text-muted">{{ $latest->created_at->format('d M Y') }}</small>
                        </div>
                    </div>
                    @endforeach
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('news.index') }}" class="btn btn-outline-primary btn-sm">
                            Lihat Semua Berita
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Categories -->
            @if($categories->count() > 0)
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-tags me-2"></i>Kategori
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($categories as $category => $count)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <a href="{{ route('news.index', ['category' => $category]) }}" class="text-decoration-none">
                            {{ ucfirst($category) }}
                        </a>
                        <span class="badge bg-secondary">{{ $count }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Quick Links -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-external-link-alt me-2"></i>Link Terkait
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('jobs.index') }}" class="list-group-item list-group-item-action border-0 px-0">
                            <i class="fas fa-briefcase me-2"></i>Lowongan Kerja
                        </a>
                        <a href="{{ route('home') }}#contact" class="list-group-item list-group-item-action border-0 px-0">
                            <i class="fas fa-envelope me-2"></i>Hubungi Kami
                        </a>
                        <a href="#" class="list-group-item list-group-item-action border-0 px-0">
                            <i class="fas fa-graduation-cap me-2"></i>Info Alumni
                        </a>
                        <a href="#" class="list-group-item list-group-item-action border-0 px-0">
                            <i class="fas fa-calendar me-2"></i>Agenda Kegiatan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Share functions
function shareToFacebook() {
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent('{{ $news->title }}');
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank', 'width=600,height=400');
}

function shareToTwitter() {
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent('{{ $news->title }}');
    window.open(`https://twitter.com/intent/tweet?text=${title}&url=${url}`, '_blank', 'width=600,height=400');
}

function shareToWhatsApp() {
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent('{{ $news->title }}');
    window.open(`https://wa.me/?text=${title} ${url}`, '_blank');
}

function copyLink() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        alert('Link berhasil disalin ke clipboard!');
    }).catch(function(err) {
        console.error('Error copying link: ', err);
        alert('Gagal menyalin link');
    });
}

// Update view count (optional - can be implemented via AJAX)
document.addEventListener('DOMContentLoaded', function() {
    // Track page view after 5 seconds
    setTimeout(function() {
        fetch(`/api/news/{{ $news->id }}/view`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        }).catch(error => {
            console.log('View tracking error:', error);
        });
    }, 5000);
});
</script>
@endpush

@section('styles')
<style>
.article-content {
    line-height: 1.8;
    font-size: 1.1rem;
}

.article-content p {
    margin-bottom: 1.5rem;
}

.social-share .btn {
    width: 40px;
    height: 40px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.list-group-item:hover {
    background-color: #f8f9fa;
}
</style>
@endsection
