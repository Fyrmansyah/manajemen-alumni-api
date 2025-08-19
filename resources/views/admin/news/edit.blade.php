@extends('layouts.app')

@section('title', 'Edit Berita - Admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-edit text-primary me-2"></i>
                        Edit Berita
                    </h1>
                    <p class="text-muted mb-0">Edit dan perbarui berita</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('admin.news.show', $news) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-eye me-1"></i>
                        Lihat
                    </a>
                    <a href="{{ route('admin.news.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Kembali
                    </a>
                </div>
            </div>

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Terdapat kesalahan:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Form Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Form Edit Berita
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.news.update', $news) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Main Content -->
                            <div class="col-lg-8">
                                <h6 class="text-warning mb-3">
                                    <i class="fas fa-file-alt me-1"></i>
                                    Konten Berita
                                </h6>
                                
                                <div class="mb-3">
                                    <label for="title" class="form-label">Judul Berita <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title', $news->title) }}" 
                                           placeholder="Masukkan judul berita yang menarik...">
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <span id="titleCount">{{ strlen($news->title) }}</span>/100 karakter (optimal: 50-60 karakter)
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="excerpt" class="form-label">Ringkasan</label>
                                    <textarea class="form-control @error('excerpt') is-invalid @enderror" 
                                              id="excerpt" name="excerpt" rows="3" 
                                              placeholder="Tulis ringkasan singkat berita...">{{ old('excerpt', $news->excerpt) }}</textarea>
                                    @error('excerpt')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <span id="excerptCount">{{ strlen($news->excerpt ?? '') }}</span>/500 karakter
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="content" class="form-label">Konten Berita <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('content') is-invalid @enderror" 
                                              id="content" name="content" rows="15" 
                                              placeholder="Tulis konten berita lengkap di sini...">{{ old('content', $news->content) }}</textarea>
                                    @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="category" class="form-label">Kategori <span class="text-danger">*</span></label>
                                        <select class="form-select @error('category') is-invalid @enderror" 
                                                id="category" name="category">
                                            <option value="">Pilih Kategori</option>
                                            <option value="info" {{ old('category', $news->category) == 'info' ? 'selected' : '' }}>Informasi</option>
                                            <option value="job" {{ old('category', $news->category) == 'job' ? 'selected' : '' }}>Lowongan Kerja</option>
                                            <option value="event" {{ old('category', $news->category) == 'event' ? 'selected' : '' }}>Kegiatan</option>
                                            <option value="announcement" {{ old('category', $news->category) == 'announcement' ? 'selected' : '' }}>Pengumuman</option>
                                            <option value="achievement" {{ old('category', $news->category) == 'achievement' ? 'selected' : '' }}>Prestasi</option>
                                        </select>
                                        @error('category')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="tags" class="form-label">Tags (Opsional)</label>
                                        <input type="text" class="form-control @error('tags') is-invalid @enderror" 
                                               id="tags" name="tags" value="{{ old('tags', $news->tags) }}" 
                                               placeholder="alumni, kerja, teknologi">
                                        @error('tags')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Pisahkan dengan koma</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Settings & Media -->
                            <div class="col-lg-4">
                                <h6 class="text-warning mb-3">
                                    <i class="fas fa-cog me-1"></i>
                                    Pengaturan
                                </h6>

                                <div class="mb-3">
                                    <label for="status" class="form-label">Status Publikasi <span class="text-danger">*</span></label>
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            id="status" name="status">
                                        <option value="draft" {{ old('status', $news->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="published" {{ old('status', $news->status) == 'published' ? 'selected' : '' }}>Terbitkan</option>
                                        <option value="archived" {{ old('status', $news->status) == 'archived' ? 'selected' : '' }}>Arsip</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <h6 class="text-warning mb-3 mt-4">
                                    <i class="fas fa-image me-1"></i>
                                    Media
                                </h6>

                                <!-- Current Image -->
                                @if($news->featured_image)
                                    <div class="mb-3">
                                        <label class="form-label">Gambar Saat Ini</label>
                                        <div class="text-center">
                                            <img src="{{ asset('storage/' . $news->featured_image) }}" 
                                                 alt="{{ $news->title }}" 
                                                 class="img-fluid rounded mb-2" 
                                                 style="max-height: 200px;">
                                            <div class="form-text">Gambar saat ini akan diganti jika Anda mengupload gambar baru</div>
                                        </div>
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <label for="featured_image" class="form-label">
                                        {{ $news->featured_image ? 'Ganti Gambar' : 'Gambar Utama' }}
                                    </label>
                                    <input type="file" class="form-control @error('featured_image') is-invalid @enderror" 
                                           id="featured_image" name="featured_image" accept="image/*">
                                    @error('featured_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Format: JPG, PNG, WebP. Maksimal 2MB</div>
                                </div>

                                <div class="mb-3">
                                    <label for="meta_description" class="form-label">Meta Description</label>
                                    <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                                              id="meta_description" name="meta_description" rows="3" 
                                              placeholder="Deskripsi untuk SEO...">{{ old('meta_description', $news->meta_description) }}</textarea>
                                    @error('meta_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <span id="metaCount">{{ strlen($news->meta_description ?? '') }}</span>/160 karakter
                                    </div>
                                </div>

                                <!-- Preview -->
                                <div class="card bg-light border-0 mt-4">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-eye me-1"></i>
                                            Preview
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div id="newsPreview">
                                            <h6 class="text-primary">{{ $news->title }}</h6>
                                            <div class="mb-2">
                                                <span class="badge bg-warning text-dark">{{ ucfirst($news->category) }}</span>
                                                <span class="badge bg-secondary">{{ ucfirst($news->status) }}</span>
                                            </div>
                                            <p class="text-muted small">{{ $news->excerpt ?: 'Ringkasan berita akan muncul di sini...' }}</p>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>
                                                {{ $news->created_at->format('d M Y') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <a href="{{ route('admin.news.show', $news) }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-times me-1"></i>
                                            Batal
                                        </a>
                                    </div>
                                    <div class="btn-group">
                                        <button type="submit" name="status" value="draft" class="btn btn-outline-warning">
                                            <i class="fas fa-save me-1"></i>
                                            Simpan sebagai Draft
                                        </button>
                                        <button type="submit" name="status" value="published" class="btn btn-success">
                                            <i class="fas fa-paper-plane me-1"></i>
                                            Simpan & Publikasikan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character counters
    const titleInput = document.getElementById('title');
    const excerptInput = document.getElementById('excerpt');
    const metaInput = document.getElementById('meta_description');
    const titleCount = document.getElementById('titleCount');
    const excerptCount = document.getElementById('excerptCount');
    const metaCount = document.getElementById('metaCount');
    
    titleInput.addEventListener('input', function() {
        titleCount.textContent = this.value.length;
        updatePreview();
    });
    
    excerptInput.addEventListener('input', function() {
        excerptCount.textContent = this.value.length;
        updatePreview();
    });

    if (metaInput) {
        metaInput.addEventListener('input', function() {
            metaCount.textContent = this.value.length;
        });
    }
    
    // Status and category change handlers
    const statusSelect = document.getElementById('status');
    const categorySelect = document.getElementById('category');
    
    statusSelect.addEventListener('change', updatePreview);
    categorySelect.addEventListener('change', updatePreview);
    
    // Preview functionality
    function updatePreview() {
        const title = titleInput.value;
        const excerpt = excerptInput.value;
        const category = categorySelect.selectedOptions[0]?.text || '';
        const status = statusSelect.selectedOptions[0]?.text || '';
        
        const previewDiv = document.getElementById('newsPreview');
        
        if (title || excerpt || category) {
            let statusBadge = status ? `<span class="badge bg-secondary">${status}</span>` : '';
            let categoryBadge = category ? `<span class="badge bg-warning text-dark">${category}</span>` : '';
            
            previewDiv.innerHTML = `
                <h6 class="text-primary">${title || 'Judul Berita'}</h6>
                <div class="mb-2">
                    ${categoryBadge}
                    ${statusBadge}
                </div>
                <p class="text-muted small">${excerpt || 'Ringkasan berita akan muncul di sini...'}</p>
                <small class="text-muted">
                    <i class="fas fa-calendar me-1"></i>
                    ${new Date().toLocaleDateString('id-ID')}
                </small>
            `;
        }
    }
    
    // Image preview
    const imageInput = document.getElementById('featured_image');
    imageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            if (file.size > 2 * 1024 * 1024) { // 2MB
                alert('Ukuran file terlalu besar! Maksimal 2MB');
                this.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewDiv = document.getElementById('newsPreview');
                const existingImage = previewDiv.querySelector('.preview-image');
                if (existingImage) {
                    existingImage.remove();
                }
                
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'preview-image img-fluid rounded mb-2';
                img.style.maxHeight = '100px';
                previewDiv.insertBefore(img, previewDiv.firstChild);
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const title = titleInput.value.trim();
        const content = document.getElementById('content').value.trim();
        const category = document.getElementById('category').value;
        
        if (!title || !content || !category) {
            e.preventDefault();
            alert('Harap lengkapi semua field yang wajib diisi!');
            return false;
        }
        
        if (title.length > 100) {
            e.preventDefault();
            alert('Judul terlalu panjang! Maksimal 100 karakter');
            return false;
        }
        
        return true;
    });
});
</script>
@endpush
