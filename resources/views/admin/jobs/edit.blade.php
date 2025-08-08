@extends('layouts.app')

@section('title', 'Edit Lowongan Kerja - Admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-edit text-primary me-2"></i>
                        Edit Lowongan Kerja
                    </h1>
                    <p class="text-muted mb-0">Edit informasi lowongan kerja: {{ $job->title }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.jobs.show', $job) }}" class="btn btn-outline-info">
                        <i class="fas fa-eye me-1"></i>
                        Lihat Detail
                    </a>
                    <a href="{{ route('admin.jobs.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Kembali
                    </a>
                </div>
            </div>

            <!-- Alert Messages -->
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

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    {{ session('info') }}
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
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-form me-2"></i>
                        Edit Form Lowongan Kerja
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.jobs.update', $job) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-lg-8">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Informasi Dasar
                                </h6>
                                
                                <div class="mb-3">
                                    <label for="title" class="form-label">Judul Lowongan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title', $job->title) }}" 
                                           placeholder="Contoh: Web Developer, Marketing Manager">
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="company_id" class="form-label">Perusahaan <span class="text-danger">*</span></label>
                                    <select class="form-select @error('company_id') is-invalid @enderror" 
                                            id="company_id" name="company_id">
                                        <option value="">Pilih Perusahaan</option>
                                        @foreach($companies as $company)
                                            <option value="{{ $company->id }}" 
                                                {{ (old('company_id', $job->company_id) == $company->id) ? 'selected' : '' }}>
                                                {{ $company->company_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('company_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="type" class="form-label">Tipe Pekerjaan <span class="text-danger">*</span></label>
                                        <select class="form-select @error('type') is-invalid @enderror" 
                                                id="type" name="type">
                                            <option value="">Pilih Tipe</option>
                                            @foreach(App\Models\Job::JOB_TYPES as $key => $label)
                                                <option value="{{ $key }}" {{ (old('type', $job->type) == $key) ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="location" class="form-label">Lokasi <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                               id="location" name="location" value="{{ old('location', $job->location) }}" 
                                               placeholder="Contoh: Surabaya, Jakarta">
                                        @error('location')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Deskripsi Pekerjaan <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="6" 
                                              placeholder="Jelaskan tentang pekerjaan ini...">{{ old('description', $job->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="requirements" class="form-label">Persyaratan <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('requirements') is-invalid @enderror" 
                                              id="requirements" name="requirements" rows="6" 
                                              placeholder="Tuliskan persyaratan yang dibutuhkan...">{{ old('requirements', $job->requirements) }}</textarea>
                                    @error('requirements')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <div class="col-lg-4">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-cog me-1"></i>
                                    Pengaturan
                                </h6>

                                <div class="mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            id="status" name="status">
                                        <option value="draft" {{ (old('status', $job->status) == 'draft') ? 'selected' : '' }}>Draft</option>
                                        <option value="active" {{ (old('status', $job->status) == 'active') ? 'selected' : '' }}>Aktif</option>
                                        <option value="closed" {{ (old('status', $job->status) == 'closed') ? 'selected' : '' }}>Ditutup</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="application_deadline" class="form-label">Batas Lamaran <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('application_deadline') is-invalid @enderror" 
                                           id="application_deadline" name="application_deadline" 
                                           value="{{ old('application_deadline', $job->application_deadline?->format('Y-m-d')) }}">
                                    @error('application_deadline')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="positions_available" class="form-label">Posisi Tersedia <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('positions_available') is-invalid @enderror" 
                                           id="positions_available" name="positions_available" 
                                           value="{{ old('positions_available', $job->positions_available) }}" 
                                           min="1" placeholder="1">
                                    @error('positions_available')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <h6 class="text-primary mb-3 mt-4">
                                    <i class="fas fa-money-bill-wave me-1"></i>
                                    Gaji (Opsional)
                                </h6>

                                <div class="mb-3">
                                    <label for="salary_min" class="form-label">Gaji Minimum</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control @error('salary_min') is-invalid @enderror" 
                                               id="salary_min" name="salary_min" 
                                               value="{{ old('salary_min', $job->salary_min) }}" 
                                               placeholder="0">
                                    </div>
                                    @error('salary_min')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="salary_max" class="form-label">Gaji Maksimum</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control @error('salary_max') is-invalid @enderror" 
                                               id="salary_max" name="salary_max" 
                                               value="{{ old('salary_max', $job->salary_max) }}" 
                                               placeholder="0">
                                    </div>
                                    @error('salary_max')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                @if($job->isArchived())
                                    <div class="alert alert-warning" role="alert">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>Perhatian:</strong> Lowongan ini sedang diarsipkan.
                                        <small class="d-block mt-1">Diarsipkan: {{ $job->archived_at->format('d M Y H:i') }}</small>
                                        <small class="d-block">Alasan: {{ $job->archive_reason ?? 'Tidak ada alasan' }}</small>
                                    </div>
                                @endif

                                <!-- Current Status Info -->
                                <div class="card bg-light border-0 mt-4">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-info me-1"></i>
                                            Info Lowongan
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <small class="text-muted">
                                            <strong>Dibuat:</strong> {{ $job->created_at->format('d M Y H:i') }}<br>
                                            <strong>Terakhir Diupdate:</strong> {{ $job->updated_at->format('d M Y H:i') }}<br>
                                            <strong>Total Lamaran:</strong> {{ $job->applications->count() }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row">
                            <div class="col-12">
                                <hr class="my-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.jobs.show', $job) }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-times me-1"></i>
                                            Batal
                                        </a>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="submit" name="action" value="save" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i>
                                            Update Lowongan
                                        </button>
                                        @if(!$job->isArchived())
                                            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#archiveModal">
                                                <i class="fas fa-archive me-1"></i>
                                                Arsipkan
                                            </button>
                                        @endif
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

<!-- Archive Modal -->
@if(!$job->isArchived())
<div class="modal fade" id="archiveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Arsipkan Lowongan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.jobs.archive', $job) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <p>Yakin ingin mengarsipkan lowongan "<strong>{{ $job->title }}</strong>"?</p>
                    <div class="mb-3">
                        <label for="reason" class="form-label">Alasan Arsip (opsional)</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" placeholder="Masukkan alasan mengapa lowongan ini diarsipkan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Arsipkan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    // Auto-update gaji preview when input changes
    document.getElementById('salary_min').addEventListener('input', updateSalaryPreview);
    document.getElementById('salary_max').addEventListener('input', updateSalaryPreview);

    function updateSalaryPreview() {
        const min = document.getElementById('salary_min').value;
        const max = document.getElementById('salary_max').value;
        // Add preview logic if needed
    }
</script>
@endpush
