@extends('layouts.app')

@section('title', 'Registrasi Perusahaan - Sistem Lamaran Kerja BKK SMKN 1 Surabaya')

@section('content')
<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-building me-2"></i>Registrasi Perusahaan
                    </h4>
                </div>
                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Perhatian:</strong> Akun perusahaan akan diverifikasi terlebih dahulu oleh admin BKK sebelum dapat digunakan dan akan dikerjakan pada saat jam kerjai.
                    </div>

                    <form method="POST" action="{{ route('company.register') }}" class="needs-validation" novalidate>
                        @csrf
                        
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-building me-2"></i>Informasi Perusahaan
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="company_name" class="form-label">Nama Perusahaan <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('company_name') is-invalid @enderror" 
                                           id="company_name" 
                                           name="company_name" 
                                           value="{{ old('company_name') }}" 
                                           required
                                           placeholder="PT/CV/UD Nama Perusahaan">
                                    @error('company_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Perusahaan <span class="text-danger">*</span></label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           required
                                           placeholder="hr@perusahaan.com">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                                    <input type="tel" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" 
                                           name="phone" 
                                           value="{{ old('phone') }}" 
                                           required
                                           placeholder="021-xxxxxxx">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="website" class="form-label">Website</label>
                                    <input type="url" 
                                           class="form-control @error('website') is-invalid @enderror" 
                                           id="website" 
                                           name="website" 
                                           value="{{ old('website') }}" 
                                           placeholder="https://www.perusahaan.com">
                                    @error('website')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Alamat Perusahaan <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" 
                                      name="address" 
                                      rows="3" 
                                      required
                                      placeholder="Alamat lengkap perusahaan">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Jenis Pekerjaan</label>
                                    <select class="form-select @error('category_id') is-invalid @enderror" 
                                            id="category_id" 
                                            name="category_id">
                                        <option value="">Pilih Jenis</option>
                                        @foreach($jurusans as $jurusan)
                                            <option value="{{ $jurusan->id }}" {{ old('category_id') == $jurusan->id ? 'selected' : '' }}>
                                                {{ $jurusan->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="established_year" class="form-label">Tahun Berdiri</label>
                                    <select class="form-select @error('established_year') is-invalid @enderror" 
                                            id="established_year" 
                                            name="established_year">
                                        <option value="">Pilih Tahun</option>
                                        @for($year = date('Y'); $year >= 1950; $year--)
                                            <option value="{{ $year }}" {{ old('established_year') == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('established_year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="company_size" class="form-label">Ukuran Perusahaan</label>
                                    <select class="form-select @error('company_size') is-invalid @enderror" 
                                            id="company_size" 
                                            name="company_size">
                                        <option value="">Pilih Ukuran</option>
                                        <option value="1-10" {{ old('company_size') == '1-10' ? 'selected' : '' }}>1-10 karyawan</option>
                                        <option value="11-50" {{ old('company_size') == '11-50' ? 'selected' : '' }}>11-50 karyawan</option>
                                        <option value="51-100" {{ old('company_size') == '51-100' ? 'selected' : '' }}>51-100 karyawan</option>
                                        <option value="101-500" {{ old('company_size') == '101-500' ? 'selected' : '' }}>101-500 karyawan</option>
                                        <option value="500+" {{ old('company_size') == '500+' ? 'selected' : '' }}>500+ karyawan</option>
                                    </select>
                                    @error('company_size')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi Perusahaan</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="4" 
                                      placeholder="Jelaskan tentang perusahaan, visi misi, dan bidang usaha">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <h5 class="text-primary mb-3 mt-4">
                            <i class="fas fa-user-tie me-2"></i>Informasi Kontak Person
                        </h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="contact_person" class="form-label">Nama Kontak Person <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('contact_person') is-invalid @enderror" 
                                           id="contact_person" 
                                           name="contact_person" 
                                           value="{{ old('contact_person') }}" 
                                           required
                                           placeholder="Nama lengkap PIC">
                                    @error('contact_person')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="contact_person_phone" class="form-label">Telepon Kontak Person <span class="text-danger">*</span></label>
                                    <input type="tel" 
                                           class="form-control @error('contact_person_phone') is-invalid @enderror" 
                                           id="contact_person_phone" 
                                           name="contact_person_phone" 
                                           value="{{ old('contact_person_phone') }}" 
                                           required
                                           placeholder="08xxxxxxxxxx">
                                    @error('contact_person_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <h5 class="text-primary mb-3 mt-4">
                            <i class="fas fa-lock me-2"></i>Keamanan Akun
                        </h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" 
                                               class="form-control @error('password') is-invalid @enderror" 
                                               id="password" 
                                               name="password" 
                                               required
                                               placeholder="Minimal 8 karakter">
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password_confirmation" 
                                           name="password_confirmation" 
                                           required
                                           placeholder="Ulangi password">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="agree_terms" required>
                            <label class="form-check-label" for="agree_terms">
                                Saya setuju dengan <a href="#" class="text-primary">syarat dan ketentuan</a> yang berlaku dan bersedia menunggu proses verifikasi dari admin BKK
                            </label>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-building me-2"></i>Daftar Perusahaan
                            </button>
                            <a href="{{ route('login') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-sign-in-alt me-2"></i>Sudah punya akun? Login
                            </a>
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
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const icon = this.querySelector('i');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });

    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    // Password confirmation validation
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('password_confirmation');

    function validatePassword() {
        if (password.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity("Password tidak cocok");
        } else {
            confirmPassword.setCustomValidity('');
        }
    }

    password.addEventListener('change', validatePassword);
    confirmPassword.addEventListener('keyup', validatePassword);
});
</script>
@endpush
