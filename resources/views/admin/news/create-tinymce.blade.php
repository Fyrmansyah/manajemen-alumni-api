@extends('layouts.app')

@section('title', 'Tambah Berita - Admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-plus text-primary me-2"></i>
                        Tambah Berita Baru
                    </h1>
                    <p class="text-muted mb-0">Buat dan publikasikan berita baru</p>
                </div>
                <div class="btn-group">
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
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-newspaper me-2"></i>
                        Form Tambah Berita dengan TinyMCE
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-lg-8">
                                <!-- Content Section -->
                                <h6 class="text-primary mb-3 mt-0">
                                    <i class="fas fa-edit me-1"></i>
                                    Konten Berita
                                </h6>

                                <div class="mb-3">
                                    <label for="title" class="form-label">Judul Berita <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title') }}" 
                                           placeholder="Masukkan judul berita...">
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Judul yang menarik akan meningkatkan engagement pembaca
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="excerpt" class="form-label">Ringkasan/Excerpt</label>
                                    <textarea class="form-control @error('excerpt') is-invalid @enderror" 
                                              id="excerpt" name="excerpt" rows="3" 
                                              placeholder="Ringkasan singkat berita...">{{ old('excerpt') }}</textarea>
                                    @error('excerpt')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Ringkasan akan ditampilkan di halaman listing berita
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="content" class="form-label">Konten Berita <span class="text-danger">*</span></label>
                                    <textarea id="content" name="content" class="form-control @error('content') is-invalid @enderror">{{ old('content') }}</textarea>
                                    @error('content')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Gunakan toolbar untuk format teks dan upload gambar langsung
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <!-- Publish Section -->
                                <h6 class="text-warning mb-3 mt-0">
                                    <i class="fas fa-cog me-1"></i>
                                    Pengaturan Publikasi
                                </h6>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="category" class="form-label">Kategori <span class="text-danger">*</span></label>
                                        <select class="form-select @error('category') is-invalid @enderror" 
                                                id="category" name="category">
                                            <option value="">Pilih kategori...</option>
                                            <option value="info" {{ old('category') == 'info' ? 'selected' : '' }}>Informasi</option>
                                            <option value="achievement" {{ old('category') == 'achievement' ? 'selected' : '' }}>Prestasi</option>
                                            <option value="job" {{ old('category') == 'job' ? 'selected' : '' }}>Lowongan Kerja</option>
                                            <option value="event" {{ old('category') == 'event' ? 'selected' : '' }}>Event/Kegiatan</option>
                                            <option value="announcement" {{ old('category') == 'announcement' ? 'selected' : '' }}>Pengumuman</option>
                                        </select>
                                        @error('category')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                        <select class="form-select @error('status') is-invalid @enderror" 
                                                id="status" name="status">
                                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                            <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Terbitkan</option>
                                            <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Arsip</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Media Section -->
                                <h6 class="text-success mb-3 mt-4">
                                    <i class="fas fa-image me-1"></i>
                                    Media
                                </h6>

                                <div class="mb-3">
                                    <label for="featured_image" class="form-label">Gambar Utama</label>
                                    <input type="file" class="form-control @error('featured_image') is-invalid @enderror" 
                                           id="featured_image" name="featured_image" accept="image/*">
                                    @error('featured_image')
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
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.news.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i>
                                        Batal
                                    </a>
                                    <div>
                                        <button type="submit" name="action" value="draft" class="btn btn-outline-warning me-2">
                                            <i class="fas fa-save me-1"></i>
                                            Simpan Draft
                                        </button>
                                        <button type="submit" name="action" value="publish" class="btn btn-success">
                                            <i class="fas fa-paper-plane me-1"></i>
                                            Terbitkan Berita
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

@push('styles')
<style>
.tox-tinymce {
    border-radius: 8px !important;
}
.tox .tox-editor-header {
    border-radius: 8px 8px 0 0 !important;
}
</style>
@endpush

@push('scripts')
<!-- TinyMCE -->
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
tinymce.init({
    selector: '#content',
    height: 400,
    menubar: false,
    plugins: [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'insertdatetime', 'media', 'table', 'help', 'wordcount'
    ],
    toolbar: 'undo redo | blocks | ' +
        'bold italic backcolor | alignleft aligncenter ' +
        'alignright alignjustify | bullist numlist outdent indent | ' +
        'removeformat | image media | help',
    content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
    images_upload_url: '{{ route("admin.news.upload-image") }}',
    images_upload_handler: function (blobInfo, success, failure, progress) {
        var xhr, formData;
        xhr = new XMLHttpRequest();
        xhr.withCredentials = false;
        xhr.open('POST', '{{ route("admin.news.upload-image") }}');
        xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        xhr.upload.onprogress = function (e) {
            progress(e.loaded / e.total * 100);
        };
        
        xhr.onload = function() {
            var json;
            
            if (xhr.status === 403) {
                failure('HTTP Error: ' + xhr.status, { remove: true });
                return;
            }
            
            if (xhr.status < 200 || xhr.status >= 300) {
                failure('HTTP Error: ' + xhr.status);
                return;
            }
            
            json = JSON.parse(xhr.responseText);
            
            if (!json || typeof json.url != 'string') {
                failure('Invalid JSON: ' + xhr.responseText);
                return;
            }
            
            success(json.url);
        };
        
        xhr.onerror = function () {
            failure('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
        };
        
        formData = new FormData();
        formData.append('upload', blobInfo.blob(), blobInfo.filename());
        
        xhr.send(formData);
    },
    automatic_uploads: true,
    file_picker_types: 'image',
    file_picker_callback: function(cb, value, meta) {
        var input = document.createElement('input');
        input.setAttribute('type', 'file');
        input.setAttribute('accept', 'image/*');
        
        input.onchange = function() {
            var file = this.files[0];
            var reader = new FileReader();
            
            reader.onload = function () {
                var id = 'blobid' + (new Date()).getTime();
                var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
                var base64 = reader.result.split(',')[1];
                var blobInfo = blobCache.create(id, file, base64);
                blobCache.add(blobInfo);
                
                cb(blobInfo.blobUri(), { title: file.name });
            };
            reader.readAsDataURL(file);
        };
        
        input.click();
    }
});
</script>
@endpush