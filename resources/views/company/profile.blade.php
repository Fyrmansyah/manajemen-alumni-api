@extends('layouts.app')

@section('title', 'Profil Perusahaan - BKK SMKN 1 Surabaya')

@section('content')
<div class="container-fluid mt-4 mb-5">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Profil Perusahaan</h1>
                    <p class="text-muted mb-0">Kelola informasi perusahaan Anda</p>
                </div>
                <div>
                    <a href="{{ route('company.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
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
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Company Profile Form -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-building me-2"></i>Informasi Perusahaan
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('company.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Company Name -->
                            <div class="col-md-6 mb-3">
                                <label for="company_name" class="form-label fw-bold">Nama Perusahaan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                       id="company_name" name="company_name" 
                                       value="{{ old('company_name', $company->company_name) }}" required>
                                @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Industry -->
                            <div class="col-md-6 mb-3">
                                <label for="industry" class="form-label fw-bold">Bidang Industri <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('industry') is-invalid @enderror" 
                                       id="industry" name="industry" 
                                       value="{{ old('industry', $company->industry) }}" required>
                                @error('industry')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Logo Upload -->
                        <div class="mb-3">
                            <label for="logo" class="form-label fw-bold">Logo Perusahaan</label>
                            <input type="file" class="form-control @error('logo') is-invalid @enderror" id="logo" name="logo" accept="image/*">
                            <div class="form-text">Format: JPG, PNG, maksimal 1MB.</div>
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($company->logo)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/company_logos/' . $company->logo) }}" alt="Logo Saat Ini" style="height:60px;" class="rounded border">
                                </div>
                            @endif
                        </div>

                        <div class="row">
                            <!-- Email -->
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label fw-bold">Email</label>
                                <input type="email" class="form-control" id="email" 
                                       value="{{ $company->email }}" readonly>
                                <div class="form-text">Email tidak dapat diubah</div>
                            </div>

                            <!-- Phone -->
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label fw-bold">Nomor Telepon <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" 
                                       value="{{ old('phone', $company->phone) }}" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="mb-3">
                            <label for="address" class="form-label fw-bold">Alamat Perusahaan <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" name="address" rows="3" required>{{ old('address', $company->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Website -->
                        <div class="mb-3">
                            <label for="website" class="form-label fw-bold">Website Perusahaan</label>
                            <input type="url" class="form-control @error('website') is-invalid @enderror" 
                                   id="website" name="website" 
                                   value="{{ old('website', $company->website) }}" 
                                   placeholder="https://www.example.com">
                            @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Deskripsi Perusahaan</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" 
                                      placeholder="Ceritakan tentang perusahaan Anda...">{{ old('description', $company->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <h6 class="font-weight-bold text-primary mb-3">
                            <i class="fas fa-user-tie me-2"></i>Informasi Kontak Person
                        </h6>

                        <div class="row">
                            <!-- Contact Person -->
                            <div class="col-md-6 mb-3">
                                <label for="contact_person" class="form-label fw-bold">Nama Kontak Person <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('contact_person') is-invalid @enderror" 
                                       id="contact_person" name="contact_person" 
                                       value="{{ old('contact_person', $company->contact_person) }}" required>
                                @error('contact_person')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Contact Position -->
                            <div class="col-md-6 mb-3">
                                <label for="contact_position" class="form-label fw-bold">Jabatan Kontak Person <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('contact_position') is-invalid @enderror" 
                                       id="contact_position" name="contact_position" 
                                       value="{{ old('contact_position', $company->contact_position) }}" required>
                                @error('contact_position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('company.dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Company Info Summary -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i>Status Perusahaan
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        @if($company->logo)
                            <img src="{{ asset('storage/company_logos/' . $company->logo) }}" class="rounded-circle" 
                                 width="80" height="80" alt="Logo {{ $company->company_name }}" style="object-fit:cover;">
                        @else
                            <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" 
                                 style="width: 80px; height: 80px;">
                                <i class="fas fa-building fa-2x text-white"></i>
                            </div>
                        @endif
                        <h5 class="mt-2 mb-1">{{ $company->company_name }}</h5>
                        <p class="text-muted small">{{ $company->industry }}</p>
                    </div>

                    <div class="row g-2">
                        <div class="col-12">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-envelope text-primary me-3"></i>
                                <small class="text-muted">{{ $company->email }}</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-phone text-primary me-3"></i>
                                <small class="text-muted">{{ $company->phone ?: 'Belum diisi' }}</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-globe text-primary me-3"></i>
                                @if($company->website)
                                    <a href="{{ $company->website }}" target="_blank" class="small text-decoration-none">
                                        {{ $company->website }}
                                    </a>
                                @else
                                    <small class="text-muted">Belum diisi</small>
                                @endif
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex align-items-start mb-2">
                                <i class="fas fa-map-marker-alt text-primary me-3 mt-1"></i>
                                <small class="text-muted">{{ $company->address ?: 'Belum diisi' }}</small>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="text-center">
                        <span class="badge bg-success mb-2">
                            <i class="fas fa-check-circle me-1"></i>Terverifikasi
                        </span>
                        <div class="small text-muted">
                            Bergabung sejak {{ $company->created_at->format('d M Y') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt me-2"></i>Aksi Cepat
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('company.jobs.create') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-plus me-2"></i>Buat Lowongan Baru
                        </a>
                        <a href="{{ route('company.jobs') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-briefcase me-2"></i>Kelola Lowongan
                        </a>
                        <a href="{{ route('company.applications') }}" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-users me-2"></i>Lihat Pelamar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.text-xs {
    font-size: 0.7rem;
}

.card {
    border: 0;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.card-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
}

.form-label.fw-bold {
    color: #5a5c69;
}

.btn {
    font-size: 0.875rem;
    border-radius: 0.35rem;
}

.alert {
    border: 0;
    border-radius: 0.35rem;
}
</style>
@endpush
