@extends('layouts.app')

@section('title', 'Tambah Lowongan Kerja - Admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-plus text-primary me-2"></i>
                        Tambah Lowongan Kerja
                    </h1>
                    <p class="text-muted mb-0">Buat lowongan kerja baru untuk alumni</p>
                </div>
                <a href="{{ route('admin.jobs.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>
                    Kembali
                </a>
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
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-form me-2"></i>
                        Form Lowongan Kerja
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.jobs.store') }}" method="POST">
                        @csrf
                        
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
                                           id="title" name="title" value="{{ old('title') }}" 
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
                                            <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
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
                                        <label for="job_type" class="form-label">Tipe Pekerjaan <span class="text-danger">*</span></label>
                                        <select class="form-select @error('job_type') is-invalid @enderror" 
                                                id="job_type" name="job_type">
                                            <option value="">Pilih Tipe</option>
                                            <option value="full-time" {{ old('job_type') == 'full-time' ? 'selected' : '' }}>Full Time</option>
                                            <option value="part-time" {{ old('job_type') == 'part-time' ? 'selected' : '' }}>Part Time</option>
                                            <option value="contract" {{ old('job_type') == 'contract' ? 'selected' : '' }}>Kontrak</option>
                                            <option value="internship" {{ old('job_type') == 'internship' ? 'selected' : '' }}>Magang</option>
                                        </select>
                                        @error('job_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="location" class="form-label">Lokasi <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                               id="location" name="location" value="{{ old('location') }}" 
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
                                              placeholder="Jelaskan tentang pekerjaan ini...">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="requirements" class="form-label">Persyaratan <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('requirements') is-invalid @enderror" 
                                              id="requirements" name="requirements" rows="6" 
                                              placeholder="Tuliskan persyaratan yang dibutuhkan...">{{ old('requirements') }}</textarea>
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
                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="deadline" class="form-label">Batas Lamaran <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('deadline') is-invalid @enderror" 
                                           id="deadline" name="deadline" value="{{ old('deadline') }}" 
                                           min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                    @error('deadline')
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
                                               id="salary_min" name="salary_min" value="{{ old('salary_min') }}" 
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
                                               id="salary_max" name="salary_max" value="{{ old('salary_max') }}" 
                                               placeholder="0">
                                    </div>
                                    @error('salary_max')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Preview Card -->
                                <div class="card bg-light border-0 mt-4">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-eye me-1"></i>
                                            Preview
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div id="jobPreview">
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
                                    <a href="{{ route('admin.jobs.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i>
                                        Batal
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>
                                        Simpan Lowongan
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
    // Preview functionality
    const form = document.querySelector('form');
    const previewDiv = document.getElementById('jobPreview');
    
    function updatePreview() {
        const title = document.getElementById('title').value;
        const company = document.getElementById('company_id').selectedOptions[0]?.text || '';
        const jobType = document.getElementById('job_type').selectedOptions[0]?.text || '';
        const location = document.getElementById('location').value;
        const deadline = document.getElementById('deadline').value;
        
        if (title || company || jobType || location) {
            previewDiv.innerHTML = `
                <h6 class="text-primary">${title || 'Judul Lowongan'}</h6>
                <p class="mb-1"><i class="fas fa-building me-1"></i> ${company || 'Nama Perusahaan'}</p>
                <p class="mb-1"><i class="fas fa-briefcase me-1"></i> ${jobType || 'Tipe Pekerjaan'}</p>
                <p class="mb-1"><i class="fas fa-map-marker-alt me-1"></i> ${location || 'Lokasi'}</p>
                ${deadline ? `<p class="mb-0"><i class="fas fa-calendar me-1"></i> Batas: ${new Date(deadline).toLocaleDateString('id-ID')}</p>` : ''}
            `;
        } else {
            previewDiv.innerHTML = '<small class="text-muted">Preview akan muncul saat Anda mengisi form</small>';
        }
    }
    
    // Update preview on input change
    ['title', 'company_id', 'job_type', 'location', 'deadline'].forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', updatePreview);
            field.addEventListener('change', updatePreview);
        }
    });
    
    // Form validation
    form.addEventListener('submit', function(e) {
        const title = document.getElementById('title').value.trim();
        const company = document.getElementById('company_id').value;
        const description = document.getElementById('description').value.trim();
        const requirements = document.getElementById('requirements').value.trim();
        
        if (!title || !company || !description || !requirements) {
            e.preventDefault();
            alert('Harap lengkapi semua field yang wajib diisi!');
            return false;
        }
        
        // Salary validation
        const salaryMin = parseFloat(document.getElementById('salary_min').value) || 0;
        const salaryMax = parseFloat(document.getElementById('salary_max').value) || 0;
        
        if (salaryMax > 0 && salaryMin > salaryMax) {
            e.preventDefault();
            alert('Gaji minimum tidak boleh lebih besar dari gaji maksimum!');
            return false;
        }
        
        return true;
    });
    
    // Format number inputs
    ['salary_min', 'salary_max'].forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', function() {
                // Remove non-numeric characters except decimal point
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        }
    });
});
</script>
@endpush
