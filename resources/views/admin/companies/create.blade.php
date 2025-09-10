@extends('layouts.app')

@section('title', 'Tambah Perusahaan - Admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-plus text-primary me-2"></i>
                        Tambah Perusahaan
                    </h1>
                    <p class="text-muted mb-0">Menambahkan perusahaan mitra BKK baru</p>
                </div>
                <a href="{{ route('admin.companies.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>
                    Kembali
                </a>
            </div>

            <!-- Form Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-building me-2"></i>
                        Informasi Perusahaan
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.companies.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <!-- Company Name -->
                            <div class="col-md-6 mb-3">
                                <label for="company_name" class="form-label">Nama Perusahaan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                       id="company_name" name="company_name" value="{{ old('company_name') }}" 
                                       placeholder="Masukkan nama perusahaan" required>
                                @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" 
                                       placeholder="email@perusahaan.com" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Phone -->
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Nomor Telepon</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone') }}" 
                                       placeholder="08xxxxxxxxxx">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Website -->
                            <div class="col-md-6 mb-3">
                                <label for="website" class="form-label">Website</label>
                                <input type="url" class="form-control @error('website') is-invalid @enderror" 
                                       id="website" name="website" value="{{ old('website') }}" 
                                       placeholder="https://www.perusahaan.com">
                                @error('website')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Password -->
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password Akun <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" 
                                       placeholder="Masukkan password untuk akun perusahaan" required>
                                <small class="form-text text-muted">Password minimal 8 karakter</small>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                                       id="password_confirmation" name="password_confirmation" 
                                       placeholder="Ulangi password" required>
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Industry -->
                            <div class="col-md-6 mb-3">
                                <label for="industry" class="form-label">Industri</label>
                                <select class="form-select @error('industry') is-invalid @enderror" id="industry" name="industry">
                                    <option value="">Pilih Industri</option>
                                    <option value="teknologi" {{ old('industry') == 'teknologi' ? 'selected' : '' }}>Teknologi</option>
                                    <option value="manufaktur" {{ old('industry') == 'manufaktur' ? 'selected' : '' }}>Manufaktur</option>
                                    <option value="perdagangan" {{ old('industry') == 'perdagangan' ? 'selected' : '' }}>Perdagangan</option>
                                    <option value="jasa" {{ old('industry') == 'jasa' ? 'selected' : '' }}>Jasa</option>
                                    <option value="pendidikan" {{ old('industry') == 'pendidikan' ? 'selected' : '' }}>Pendidikan</option>
                                    <option value="kesehatan" {{ old('industry') == 'kesehatan' ? 'selected' : '' }}>Kesehatan</option>
                                    <option value="keuangan" {{ old('industry') == 'keuangan' ? 'selected' : '' }}>Keuangan</option>
                                    <option value="properti" {{ old('industry') == 'properti' ? 'selected' : '' }}>Properti</option>
                                    <option value="transportasi" {{ old('industry') == 'transportasi' ? 'selected' : '' }}>Transportasi</option>
                                    <option value="lainnya" {{ old('industry') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                @error('industry')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi Perusahaan</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" 
                                      placeholder="Deskripsi singkat tentang perusahaan...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Logo Upload -->
                        <div class="mb-3">
                            <label for="logo" class="form-label">Logo Perusahaan</label>
                            <input type="file" class="form-control @error('logo') is-invalid @enderror" 
                                   id="logo" name="logo" accept="image/*">
                            <small class="form-text text-muted">Format: JPG, PNG, GIF. Maksimal 2MB.</small>
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Verification Status -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_verified" name="is_verified" value="1" {{ old('is_verified') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_verified">
                                        Terverifikasi
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_approved" name="is_approved" value="1" {{ old('is_approved') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_approved">
                                        Disetujui
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.companies.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                Simpan Perusahaan
                            </button>
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
.form-label {
    font-weight: 600;
    color: #374151;
}

.card {
    border-radius: 12px;
}

.card-header {
    border-bottom: 1px solid #e5e7eb;
    border-radius: 12px 12px 0 0 !important;
}

.form-control:focus, .form-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25);
}

.btn {
    border-radius: 8px;
}

.password-strength {
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.password-strength.weak { color: #dc3545; }
.password-strength.medium { color: #ffc107; }
.password-strength.strong { color: #198754; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    
    // Password strength checker
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        const strengthDiv = document.getElementById('password-strength');
        
        if (!strengthDiv) {
            const div = document.createElement('div');
            div.id = 'password-strength';
            div.className = 'password-strength';
            this.parentNode.appendChild(div);
        }
        
        const strength = checkPasswordStrength(password);
        document.getElementById('password-strength').innerHTML = strength.message;
        document.getElementById('password-strength').className = `password-strength ${strength.class}`;
    });
    
    // Password confirmation checker
    confirmPasswordInput.addEventListener('input', function() {
        const password = passwordInput.value;
        const confirmPassword = this.value;
        
        if (confirmPassword && password !== confirmPassword) {
            this.setCustomValidity('Password tidak sama');
            this.classList.add('is-invalid');
        } else {
            this.setCustomValidity('');
            this.classList.remove('is-invalid');
        }
    });
    
    function checkPasswordStrength(password) {
        if (password.length < 8) {
            return { message: 'Password terlalu pendek (minimal 8 karakter)', class: 'weak' };
        }
        
        let score = 0;
        if (password.length >= 8) score++;
        if (/[a-z]/.test(password)) score++;
        if (/[A-Z]/.test(password)) score++;
        if (/[0-9]/.test(password)) score++;
        if (/[^A-Za-z0-9]/.test(password)) score++;
        
        if (score < 3) {
            return { message: 'Password lemah', class: 'weak' };
        } else if (score < 4) {
            return { message: 'Password sedang', class: 'medium' };
        } else {
            return { message: 'Password kuat', class: 'strong' };
        }
    }
});
</script>
@endpush
