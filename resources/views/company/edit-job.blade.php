@extends('layouts.app')

@section('title', 'Edit Lowongan - BKK SMKN 1 Surabaya')

@section('content')
<div class="container-fluid mt-4 mb-5">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Edit Lowongan</h1>
                    <p class="text-muted mb-0">Perbarui detail lowongan kerja Anda</p>
                </div>
                <div>
                    <a href="{{ route('company.jobs') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Lowongan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-edit me-2"></i>Edit Lowongan Kerja
                    </h6>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form id="jobEditForm" method="POST" action="{{ route('company.jobs.update', $job->id) }}">
                        @csrf
                        @method('PUT')
                        <!-- Job Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Judul Lowongan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" required 
                                   value="{{ old('title', $job->title) }}">
                            <div class="invalid-feedback"></div>
                        </div>
                        <!-- Job Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi Pekerjaan <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="5" required>{{ old('description', $job->description) }}</textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                        <!-- Job Requirements -->
                        <div class="mb-3">
                            <label for="requirements" class="form-label">Persyaratan <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="requirements" name="requirements" rows="4" required>{{ old('requirements', $job->requirements) }}</textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="row">
                            <!-- Location -->
                            <div class="col-md-6 mb-3">
                                <label for="location" class="form-label">Lokasi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="location" name="location" required
                                       value="{{ old('location', $job->location) }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <!-- Job Type -->
                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label">Tipe Pekerjaan <span class="text-danger">*</span></label>
                                <select class="form-select" id="job_type" name="job_type" required>
                                    <option value="">Pilih tipe pekerjaan</option>
                                    @foreach($jobTypes as $key => $value)
                                        <option value="{{ $key }}" {{ old('job_type', $job->type) == $key ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <!-- Salary Section (Optional) -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">Rentang Gaji (Opsional)</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="includeSalary" {{ ($job->salary_min || $job->salary_max) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="includeSalary">
                                        Tampilkan gaji
                                    </label>
                                </div>
                            </div>
                            <div id="salarySection" style="display: {{ ($job->salary_min || $job->salary_max) ? 'block' : 'none' }};">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="salary_min" class="form-label">Gaji Minimum</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control" id="salary_min" name="salary_min" 
                                                   placeholder="0" min="0" value="{{ old('salary_min', $job->salary_min) }}">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="salary_max" class="form-label">Gaji Maksimum</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control" id="salary_max" name="salary_max" 
                                                   placeholder="0" min="0" value="{{ old('salary_max', $job->salary_max) }}">
                                        </div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Kosongkan jika tidak ingin menampilkan informasi gaji
                                </small>
                            </div>
                        </div>
                        <div class="row">
                            <!-- Application Deadline -->
                            <div class="col-md-6 mb-3">
                                <label for="application_deadline" class="form-label">Batas Lamaran <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="application_deadline" name="application_deadline" 
                                       required value="{{ old('application_deadline', $job->application_deadline ? date('Y-m-d', strtotime($job->application_deadline)) : '') }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <!-- Positions Available -->
                            <div class="col-md-6 mb-3">
                                <label for="positions_available" class="form-label">Jumlah Posisi <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="positions_available" name="positions_available" 
                                       required min="1" value="{{ old('positions_available', $job->positions_available) }}">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('company.jobs') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-save me-2"></i>Update Lowongan
                            </button>
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
    const includeSalaryCheckbox = document.getElementById('includeSalary');
    const salarySection = document.getElementById('salarySection');
    const salaryMinInput = document.getElementById('salary_min');
    const salaryMaxInput = document.getElementById('salary_max');
    const jobForm = document.getElementById('jobEditForm');
    const submitBtn = document.getElementById('submitBtn');

    // Toggle salary section visibility
    includeSalaryCheckbox.addEventListener('change', function() {
        if (this.checked) {
            salarySection.style.display = 'block';
        } else {
            salarySection.style.display = 'none';
            salaryMinInput.value = '';
            salaryMaxInput.value = '';
        }
    });

    // Salary validation
    salaryMaxInput.addEventListener('input', function() {
        const minSalary = parseFloat(salaryMinInput.value) || 0;
        const maxSalary = parseFloat(this.value) || 0;
        if (maxSalary > 0 && minSalary > 0 && maxSalary < minSalary) {
            this.setCustomValidity('Gaji maksimum harus lebih besar atau sama dengan gaji minimum');
        } else {
            this.setCustomValidity('');
        }
    });

    // Form submission
    jobForm.addEventListener('submit', function(e) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        if (!includeSalaryCheckbox.checked) {
            salaryMinInput.value = '';
            salaryMaxInput.value = '';
        }
    });
});
</script>
@endpush
