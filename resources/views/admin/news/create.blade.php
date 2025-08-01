@extends('layouts.app')

@section('title', 'Buat Berita - Admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-newspaper text-primary me-2"></i>
                        Buat Berita Baru
                    </h1>
                    <p class="text-muted mb-0">Buat dan publikasikan berita untuk alumni dan perusahaan</p>
                </div>
                <a href="{{ route('admin.news.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>
                    Kembali
                </a>
            </div>

            <!-- Alert Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

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
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Form Berita
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <!-- Main Content -->
                            <div class="col-lg-8">
                                <h6 class="text-success mb-3">
                                    <i class="fas fa-file-alt me-1"></i>
                                    Konten Berita
                                </h6>
                                
                                <div class="mb-3">
                                    <label for="title" class="form-label">Judul Berita <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title') }}" 
                                           placeholder="Masukkan judul berita yang menarik...">
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <span id="titleCount">0</span>/100 karakter (optimal: 50-60 karakter)
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="excerpt" class="form-label">Ringkasan <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('excerpt') is-invalid @enderror" 
                                              id="excerpt" name="excerpt" rows="3" 
                                              placeholder="Tulis ringkasan singkat berita...">{{ old('excerpt') }}</textarea>
                                    @error('excerpt')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <span id="excerptCount">0</span>/200 karakter
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="content" class="form-label">Konten Berita <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('content') is-invalid @enderror" 
                                              id="content" name="content" rows="15" 
                                              placeholder="Tulis konten berita lengkap di sini...">{{ old('content') }}</textarea>
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
                                            <option value="info" {{ old('category') == 'info' ? 'selected' : '' }}>Informasi</option>
                                            <option value="lowongan" {{ old('category') == 'lowongan' ? 'selected' : '' }}>Lowongan Kerja</option>
                                            <option value="kegiatan" {{ old('category') == 'kegiatan' ? 'selected' : '' }}>Kegiatan</option>
                                            <option value="pengumuman" {{ old('category') == 'pengumuman' ? 'selected' : '' }}>Pengumuman</option>
                                            <option value="prestasi" {{ old('category') == 'prestasi' ? 'selected' : '' }}>Prestasi</option>
                                        </select>
                                        @error('category')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="tags" class="form-label">Tags (Opsional)</label>
                                        <input type="text" class="form-control @error('tags') is-invalid @enderror" 
                                               id="tags" name="tags" value="{{ old('tags') }}" 
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
                                <h6 class="text-success mb-3">
                                    <i class="fas fa-cog me-1"></i>
                                    Pengaturan
                                </h6>

                                <div class="mb-3">
                                    <label for="status" class="form-label">Status Publikasi <span class="text-danger">*</span></label>
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            id="status" name="status">
                                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Terbitkan</option>
                                        <option value="scheduled" {{ old('status') == 'scheduled' ? 'selected' : '' }}>Jadwalkan</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3" id="publishDateGroup" style="display: none;">
                                    <label for="published_at" class="form-label">Tanggal Publikasi</label>
                                    <input type="datetime-local" class="form-control @error('published_at') is-invalid @enderror" 
                                           id="published_at" name="published_at" value="{{ old('published_at') }}">
                                    @error('published_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="featured" class="form-label">Berita Unggulan</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="featured" name="featured" value="1" {{ old('featured') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="featured">
                                            Jadikan berita unggulan
                                        </label>
                                    </div>
                                    <div class="form-text">Berita unggulan akan ditampilkan di halaman utama</div>
                                </div>

                                <h6 class="text-success mb-3 mt-4">
                                    <i class="fas fa-image me-1"></i>
                                    Media
                                </h6>

                                <div class="mb-3">
                                    <label for="image" class="form-label">Gambar Utama</label>
                                    <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                           id="image" name="image" accept="image/*">
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Format: JPG, PNG, WebP. Maksimal 2MB</div>
                                </div>

                                <div class="mb-3">
                                    <label for="image_caption" class="form-label">Caption Gambar</label>
                                    <input type="text" class="form-control @error('image_caption') is-invalid @enderror" 
                                           id="image_caption" name="image_caption" value="{{ old('image_caption') }}" 
                                           placeholder="Deskripsi gambar...">
                                    @error('image_caption')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                                            <small class="text-muted">Preview akan muncul saat Anda mengisi form</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <hr>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.news.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i>
                                        Batal
                                    </a>
                                    <button type="submit" name="action" value="draft" class="btn btn-outline-warning">
                                        <i class="fas fa-save me-1"></i>
                                        Simpan Draft
                                    </button>
                                    <button type="submit" name="action" value="publish" class="btn btn-success">
                                        <i class="fas fa-paper-plane me-1"></i>
                                        Publikasikan
                                    </button>
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
    const titleCount = document.getElementById('titleCount');
    const excerptCount = document.getElementById('excerptCount');
    
    titleInput.addEventListener('input', function() {
        titleCount.textContent = this.value.length;
        updatePreview();
    });
    
    excerptInput.addEventListener('input', function() {
        excerptCount.textContent = this.value.length;
        updatePreview();
    });
    
    // Status change handler
    const statusSelect = document.getElementById('status');
    const publishDateGroup = document.getElementById('publishDateGroup');
    
    statusSelect.addEventListener('change', function() {
        if (this.value === 'scheduled') {
            publishDateGroup.style.display = 'block';
        } else {
            publishDateGroup.style.display = 'none';
        }
        updatePreview();
    });
    
    // Preview functionality
    function updatePreview() {
        const title = titleInput.value;
        const excerpt = excerptInput.value;
        const category = document.getElementById('category').selectedOptions[0]?.text || '';
        const status = statusSelect.selectedOptions[0]?.text || '';
        const featured = document.getElementById('featured').checked;
        
        const previewDiv = document.getElementById('newsPreview');
        
        if (title || excerpt || category) {
            let featuredBadge = featured ? '<span class="badge bg-warning text-dark">Unggulan</span>' : '';
            let statusBadge = status ? `<span class="badge bg-secondary">${status}</span>` : '';
            
            previewDiv.innerHTML = `
                <h6 class="text-primary">${title || 'Judul Berita'}</h6>
                <div class="mb-2">
                    ${featuredBadge}
                    ${statusBadge}
                    ${category ? `<span class="badge bg-info">${category}</span>` : ''}
                </div>
                <p class="text-muted small">${excerpt || 'Ringkasan berita akan muncul di sini...'}</p>
                <small class="text-muted">
                    <i class="fas fa-calendar me-1"></i>
                    ${new Date().toLocaleDateString('id-ID')}
                </small>
            `;
        } else {
            previewDiv.innerHTML = '<small class="text-muted">Preview akan muncul saat Anda mengisi form</small>';
        }
    }
    
    // Image preview
    const imageInput = document.getElementById('image');
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
        const excerpt = excerptInput.value.trim();
        const content = document.getElementById('content').value.trim();
        const category = document.getElementById('category').value;
        
        if (!title || !excerpt || !content || !category) {
            e.preventDefault();
            alert('Harap lengkapi semua field yang wajib diisi!');
            return false;
        }
        
        if (title.length > 100) {
            e.preventDefault();
            alert('Judul terlalu panjang! Maksimal 100 karakter');
            return false;
        }
        
        if (excerpt.length > 200) {
            e.preventDefault();
            alert('Ringkasan terlalu panjang! Maksimal 200 karakter');
            return false;
        }
        
        return true;
    });
    
    // Initialize preview
    updatePreview();
    
    // Update preview on category change
    document.getElementById('category').addEventListener('change', updatePreview);
    document.getElementById('featured').addEventListener('change', updatePreview);
    document.getElementById('content').addEventListener('input', updatePreview);
});
</script>
@endpush
