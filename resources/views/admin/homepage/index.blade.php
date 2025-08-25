@extends('layouts.app')
@section('title','Kelola Homepage')
@section('content')
<div class="container mt-4 mb-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-2">Kelola Homepage</h1>
        <div>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAddSlide"><i class="fas fa-plus me-1"></i> Slide</button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0 small">
                @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
            </ul>
        </div>
    @endif

    <ul class="nav nav-pills mb-4" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-settings" data-bs-toggle="pill" data-bs-target="#pane-settings" type="button" role="tab">Pengaturan</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-slides" data-bs-toggle="pill" data-bs-target="#pane-slides" type="button" role="tab">Slides <span class="badge bg-secondary ms-1">{{ $slides->count() }}</span></button>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="pane-settings" role="tabpanel">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex align-items-center gap-2">
                    <i class="fas fa-cog text-primary"></i>
                    <strong>Pengaturan Hero & Slider</strong>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.homepage.settings.update') }}" class="row g-3">
                        @csrf
                        @method('PUT')
                        <div class="col-md-6">
                            <label class="form-label">Judul (Hero Title)</label>
                            <input type="text" name="hero_title" class="form-control" value="{{ old('hero_title',$setting->hero_title) }}" placeholder="Contoh: Portal Karir Alumni">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sub Judul</label>
                            <input type="text" name="hero_subtitle" class="form-control" value="{{ old('hero_subtitle',$setting->hero_subtitle) }}" placeholder="Subjudul singkat (opsional)">
                        </div>
                        <div class="col-md-3 form-check form-switch ms-2">
                            <input class="form-check-input" type="checkbox" name="show_slider" value="1" id="show_slider" {{ $setting->show_slider? 'checked':'' }}>
                            <label class="form-check-label" for="show_slider">Tampilkan Slider</label>
                        </div>
                        <div class="col-md-4 form-check form-switch ms-2">
                            <input class="form-check-input" type="checkbox" name="show_hero_text" value="1" id="show_hero_text" {{ $setting->show_hero_text? 'checked':'' }}>
                            <label class="form-check-label" for="show_hero_text">Tampilkan Teks Hero</label>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan Pengaturan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="pane-slides" role="tabpanel">
            @if($slides->isEmpty())
                <div class="text-center text-muted py-5 border rounded bg-white shadow-sm">
                    <i class="fas fa-images fa-2x mb-3"></i>
                    <p class="mb-3">Belum ada slide. Tambahkan slide baru.</p>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAddSlide"><i class="fas fa-plus me-1"></i>Tambah Slide</button>
                </div>
            @else
                <div class="row g-4">
                    @foreach($slides as $slide)
                    <div class="col-md-6 col-xl-4">
                        <div class="slide-card card h-100 shadow-sm {{ $slide->is_active ? 'is-active' : 'is-inactive' }}">
                            <div class="ratio ratio-4x3 slide-thumb-wrapper">
                                <img src="{{ asset('storage/'.$slide->image) }}" class="slide-thumb" alt="thumb">
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h6 class="mb-1 text-truncate" title="{{ $slide->title }}">{{ $slide->title ?: '—' }}</h6>
                                <p class="text-muted small mb-3 line-clamp-3">{{ $slide->caption }}</p>
                                <div class="mt-auto d-flex gap-2 flex-wrap align-items-center">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditSlide{{ $slide->id }}" title="Edit"><i class="fas fa-edit"></i></button>
                                    <form method="POST" action="{{ route('admin.homepage.slides.update',$slide->id) }}" class="m-0 p-0">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="title" value="{{ $slide->title }}">
                                        <input type="hidden" name="caption" value="{{ $slide->caption }}">
                                        <input type="hidden" name="button_text" value="{{ $slide->button_text }}">
                                        <input type="hidden" name="button_link" value="{{ $slide->button_link }}">
                                        <input type="hidden" name="sort_order" value="{{ $slide->sort_order }}">
                                        <input type="hidden" name="is_active" value="{{ $slide->is_active? 0:1 }}">
                                        <button class="btn btn-sm toggle-active-btn {{ $slide->is_active? 'btn-success':'btn-outline-secondary' }}" title="{{ $slide->is_active? 'Nonaktifkan':'Aktifkan' }}">
                                            <i class="fas {{ $slide->is_active? 'fa-toggle-on':'fa-toggle-off' }}"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.homepage.slides.delete',$slide->id) }}" onsubmit="return confirm('Hapus slide ini?')" class="m-0 p-0">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="modalEditSlide{{ $slide->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Slide</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST" action="{{ route('admin.homepage.slides.update',$slide->id) }}" enctype="multipart/form-data" class="needs-validation" novalidate>
                                    @csrf @method('PUT')
                                    <div class="modal-body row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Judul</label>
                                            <input type="text" name="title" class="form-control" value="{{ $slide->title }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Urutan</label>
                                            <input type="number" name="sort_order" class="form-control" value="{{ $slide->sort_order }}" min="0">
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label">Caption</label>
                                            <textarea name="caption" rows="2" class="form-control">{{ $slide->caption }}</textarea>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Teks Tombol</label>
                                            <input type="text" name="button_text" class="form-control" value="{{ $slide->button_text }}">
                                        </div>
                                        <div class="col-md-8">
                                            <label class="form-label">Link Tombol</label>
                                            <input type="url" name="button_link" class="form-control" value="{{ $slide->button_link }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Gambar (opsional)</label>
                                            <input type="file" name="image" class="form-control">
                                            <small class="text-muted">Biarkan kosong jika tidak diganti.</small>
                                        </div>
                                        <div class="col-md-6 form-check form-switch mt-4 pt-2">
                                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active_modal{{ $slide->id }}" {{ $slide->is_active? 'checked':'' }}>
                                            <label class="form-check-label" for="is_active_modal{{ $slide->id }}">Aktif</label>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                        <button class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Add Slide Modal -->
<div class="modal fade" id="modalAddSlide" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-plus-circle me-1 text-success"></i>Tambah Slide</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="{{ route('admin.homepage.slides.store') }}" enctype="multipart/form-data" class="needs-validation" novalidate>
        @csrf
        <div class="modal-body row g-3">
            <div class="col-md-6">
                <label class="form-label">Judul</label>
                <input type="text" name="title" class="form-control" placeholder="Judul slide">
            </div>
            <div class="col-md-6">
                <label class="form-label">Gambar <span class="text-danger">*</span></label>
                <input type="file" name="image" class="form-control" required>
            </div>
            <div class="col-12">
                <label class="form-label">Caption</label>
                <textarea name="caption" rows="2" class="form-control" placeholder="Deskripsi singkat"></textarea>
            </div>
            <div class="col-md-4">
                <label class="form-label">Teks Tombol</label>
                <input type="text" name="button_text" class="form-control" placeholder="Misal: Baca Lebih">
            </div>
            <div class="col-md-8">
                <label class="form-label">Link Tombol</label>
                <input type="url" name="button_link" class="form-control" placeholder="https://...">
            </div>
            <div class="col-12 form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="add_is_active_modal" checked>
                <label class="form-check-label" for="add_is_active_modal">Aktif</label>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
            <button class="btn btn-success"><i class="fas fa-save me-1"></i> Simpan Slide</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
.nav-pills .nav-link{font-weight:500;border-radius:30px;padding:.5rem 1.25rem;}
.nav-pills .nav-link.active{background:#0d6efd;}
.slide-card .slide-thumb-wrapper{background:#f8f9fa;border-bottom:1px solid #eef2f6;}
.slide-thumb{width:100%;height:100%;object-fit:cover;}
.slide-card.is-active{border:2px solid #16a34a; box-shadow:0 0 0 3px rgba(34,197,94,.25);}
.slide-card.is-inactive{opacity:.65; filter:grayscale(.15);}
.slide-card.is-inactive .badge.bg-success{background:#6c757d !important;}
.toggle-active-btn{min-width:40px;}
.toggle-active-btn.btn-success{display:flex;align-items:center;justify-content:center;}
.toggle-active-btn.btn-outline-secondary{color:#6c757d;}
.toggle-active-btn.btn-outline-secondary:hover{background:#e9ecef;}
.line-clamp-3{display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;}
.ratio-4x3{--bs-aspect-ratio:75%;}
.modal .form-label{font-weight:500;}
/* Remove blue overlay covering slide images (force show original photo) */
.slide-thumb-wrapper{background:transparent !important;}
.slide-thumb{position:relative;z-index:1;}
.slide-thumb-wrapper::before,
.slide-thumb-wrapper::after{display:none !important;background:transparent !important;}
</style>
@endpush
