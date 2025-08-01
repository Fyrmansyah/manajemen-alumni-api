@extends('layouts.app')

@section('title', 'Buat CV Baru')

@push('styles')
<style>
.card-header.bg-light {
    background-color: #f8f9fa !important;
    border-bottom: 1px solid #dee2e6;
}

.experience-item, .education-item {
    transition: all 0.3s ease;
}

.experience-item:hover, .education-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.remove-experience, .remove-education {
    transition: all 0.3s ease;
}

.btn-outline-danger:hover {
    transform: translateY(-1px);
}

.form-label {
    font-weight: 500;
    color: #495057;
}

.form-control:focus, .form-select:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.alert {
    border: none;
    border-radius: 0.5rem;
}

.card {
    border: none;
    border-radius: 0.75rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.btn {
    border-radius: 0.5rem;
    font-weight: 500;
}

.custom-form-section {
    max-height: 70vh;
    overflow-y: auto;
    padding-right: 1rem;
}

.custom-form-section::-webkit-scrollbar {
    width: 6px;
}

.custom-form-section::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.custom-form-section::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

.custom-form-section::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
}
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-plus me-2"></i>
                        Buat CV Baru
                    </h4>
                </div>
                
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('alumni.cv.store') }}" method="POST" id="cvForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="title" class="form-label">
                                        <i class="fas fa-heading me-1"></i>
                                        Judul CV <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('title') is-invalid @enderror" 
                                           id="title" 
                                           name="title" 
                                           value="{{ old('title') }}" 
                                           placeholder="Contoh: CV Saya - 2024"
                                           required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="template" class="form-label">
                                        <i class="fas fa-palette me-1"></i>
                                        Template <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('template') is-invalid @enderror" 
                                            id="template" 
                                            name="template" 
                                            required>
                                        <option value="">Pilih Template</option>
                                        <option value="modern" {{ old('template') == 'modern' ? 'selected' : '' }}>
                                            Modern - Clean & Professional
                                        </option>
                                        <option value="classic" {{ old('template') == 'classic' ? 'selected' : '' }}>
                                            Classic - Traditional & Formal
                                        </option>
                                        <option value="creative" {{ old('template') == 'creative' ? 'selected' : '' }}>
                                            Creative - Colorful & Dynamic
                                        </option>
                                    </select>
                                    @error('template')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-database me-1"></i>
                                Sumber Data <span class="text-danger">*</span>
                            </label>
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="radio" 
                                       name="data_source" 
                                       id="profile_data" 
                                       value="profile" 
                                       {{ old('data_source', 'profile') == 'profile' ? 'checked' : '' }}
                                       required>
                                <label class="form-check-label" for="profile_data">
                                    <strong>Gunakan Data Profile</strong>
                                    <br>
                                    <small class="text-muted">
                                        Menggunakan data dari profile alumni yang sudah lengkap
                                    </small>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="radio" 
                                       name="data_source" 
                                       id="custom_data" 
                                       value="custom" 
                                       {{ old('data_source') == 'custom' ? 'checked' : '' }}
                                       required>
                                <label class="form-check-label" for="custom_data">
                                    <strong>Input Data Manual</strong>
                                    <br>
                                    <small class="text-muted">
                                        Mengisi data CV secara manual
                                    </small>
                                </label>
                            </div>
                        </div>

                        <!-- Custom Data Form (initially hidden) -->
                        <div id="customDataForm" style="display: none;">
                            <hr class="my-4">
                            <h5 class="mb-3">
                                <i class="fas fa-edit me-2"></i>
                                Data CV Manual
                            </h5>
                            
                            <div class="custom-form-section">
                                <!-- Detail Pribadi Section -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="fas fa-user me-2"></i>
                                        Detail Pribadi
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="custom_name" class="form-label">Nama depan <span class="text-danger">*</span></label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="custom_name" 
                                                       name="custom_name" 
                                                       value="{{ old('custom_name') }}"
                                                       placeholder="Julian Fernando Ekapanca">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="custom_last_name" class="form-label">Nama belakang <span class="text-danger">*</span></label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="custom_last_name" 
                                                       name="custom_last_name" 
                                                       value="{{ old('custom_last_name') }}"
                                                       placeholder="Putra">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="custom_email" class="form-label">Alamat email <span class="text-danger">*</span></label>
                                                <input type="email" 
                                                       class="form-control" 
                                                       id="custom_email" 
                                                       name="custom_email" 
                                                       value="{{ old('custom_email') }}"
                                                       placeholder="sjulian.fernando13@gmail.com">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="custom_phone" class="form-label">Nomor telepon</label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="custom_phone" 
                                                       name="custom_phone" 
                                                       value="{{ old('custom_phone') }}"
                                                       placeholder="+62 812 3456 7890">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="custom_address" class="form-label">Alamat</label>
                                        <textarea class="form-control" 
                                                  id="custom_address" 
                                                  name="custom_address" 
                                                  rows="2"
                                                  placeholder="Jl. Contoh No. 123, Jakarta">{{ old('custom_address') }}</textarea>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="custom_postal_code" class="form-label">Kode pos</label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="custom_postal_code" 
                                                       name="custom_postal_code" 
                                                       value="{{ old('custom_postal_code') }}"
                                                       placeholder="12345">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="custom_city" class="form-label">Kota</label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="custom_city" 
                                                       name="custom_city" 
                                                       value="{{ old('custom_city') }}"
                                                       placeholder="mis. Jakarta">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="custom_birth_place" class="form-label">Tempat Lahir</label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="custom_birth_place" 
                                                       name="custom_birth_place" 
                                                       value="{{ old('custom_birth_place') }}"
                                                       placeholder="Jakarta">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="custom_birth_date" class="form-label">Tanggal Lahir</label>
                                                <input type="date" 
                                                       class="form-control" 
                                                       id="custom_birth_date" 
                                                       name="custom_birth_date" 
                                                       value="{{ old('custom_birth_date') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="custom_gender" class="form-label">Jenis Kelamin</label>
                                                <select class="form-select" id="custom_gender" name="custom_gender">
                                                    <option value="">Pilih Jenis Kelamin</option>
                                                    <option value="Laki-laki" {{ old('custom_gender') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                                    <option value="Perempuan" {{ old('custom_gender') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="custom_nationality" class="form-label">Kewarganegaraan</label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="custom_nationality" 
                                                       name="custom_nationality" 
                                                       value="{{ old('custom_nationality', 'Indonesia') }}"
                                                       placeholder="Indonesia">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pengalaman Kerja Section -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="fas fa-briefcase me-2"></i>
                                        Pengalaman Kerja
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div id="experience-container">
                                        <div class="experience-item border rounded p-3 mb-3">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Jabatan</label>
                                                        <input type="text" 
                                                               class="form-control" 
                                                               name="custom_job_title[]" 
                                                               placeholder="Staff IT">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Perusahaan</label>
                                                        <input type="text" 
                                                               class="form-control" 
                                                               name="custom_company[]" 
                                                               placeholder="PT. Teknologi Indonesia">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label">Mulai</label>
                                                        <select class="form-select" name="custom_start_month[]">
                                                            <option value="">Bulan</option>
                                                            <option value="01">Januari</option>
                                                            <option value="02">Februari</option>
                                                            <option value="03">Maret</option>
                                                            <option value="04">April</option>
                                                            <option value="05">Mei</option>
                                                            <option value="06">Juni</option>
                                                            <option value="07">Juli</option>
                                                            <option value="08">Agustus</option>
                                                            <option value="09">September</option>
                                                            <option value="10">Oktober</option>
                                                            <option value="11">November</option>
                                                            <option value="12">Desember</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label">&nbsp;</label>
                                                        <select class="form-select" name="custom_start_year[]">
                                                            <option value="">Tahun</option>
                                                            @for($year = date('Y'); $year >= 1990; $year--)
                                                                <option value="{{ $year }}">{{ $year }}</option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label">Berakhir</label>
                                                        <select class="form-select" name="custom_end_month[]">
                                                            <option value="">Bulan</option>
                                                            <option value="current">Saat ini</option>
                                                            <option value="01">Januari</option>
                                                            <option value="02">Februari</option>
                                                            <option value="03">Maret</option>
                                                            <option value="04">April</option>
                                                            <option value="05">Mei</option>
                                                            <option value="06">Juni</option>
                                                            <option value="07">Juli</option>
                                                            <option value="08">Agustus</option>
                                                            <option value="09">September</option>
                                                            <option value="10">Oktober</option>
                                                            <option value="11">November</option>
                                                            <option value="12">Desember</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label">&nbsp;</label>
                                                        <select class="form-select" name="custom_end_year[]">
                                                            <option value="">Tahun</option>
                                                            @for($year = date('Y'); $year >= 1990; $year--)
                                                                <option value="{{ $year }}">{{ $year }}</option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Deskripsi</label>
                                                <textarea class="form-control" 
                                                          name="custom_job_description[]" 
                                                          rows="3"
                                                          placeholder="Mengelola sistem IT perusahaan, maintenance server, troubleshooting..."></textarea>
                                            </div>
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-experience" style="display: none;">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="add-experience">
                                        <i class="fas fa-plus me-1"></i> Tambah Pengalaman Kerja
                                    </button>
                                </div>
                            </div>

                            <!-- Pendidikan Section -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="fas fa-graduation-cap me-2"></i>
                                        Pendidikan dan Kualifikasi
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div id="education-container">
                                        <div class="education-item border rounded p-3 mb-3">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Nama Sekolah/Universitas</label>
                                                        <input type="text" 
                                                               class="form-control" 
                                                               name="custom_school_name[]" 
                                                               placeholder="SMKN 1 Surabaya">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Gelar/Jurusan</label>
                                                        <input type="text" 
                                                               class="form-control" 
                                                               name="custom_degree[]" 
                                                               placeholder="Teknik Komputer dan Jaringan">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label">Mulai</label>
                                                        <select class="form-select" name="custom_edu_start_month[]">
                                                            <option value="">Bulan</option>
                                                            <option value="01">Januari</option>
                                                            <option value="02">Februari</option>
                                                            <option value="03">Maret</option>
                                                            <option value="04">April</option>
                                                            <option value="05">Mei</option>
                                                            <option value="06">Juni</option>
                                                            <option value="07">Juli</option>
                                                            <option value="08">Agustus</option>
                                                            <option value="09">September</option>
                                                            <option value="10">Oktober</option>
                                                            <option value="11">November</option>
                                                            <option value="12">Desember</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label">&nbsp;</label>
                                                        <select class="form-select" name="custom_edu_start_year[]">
                                                            <option value="">Tahun</option>
                                                            @for($year = date('Y'); $year >= 1990; $year--)
                                                                <option value="{{ $year }}">{{ $year }}</option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label">Berakhir</label>
                                                        <select class="form-select" name="custom_edu_end_month[]">
                                                            <option value="">Bulan</option>
                                                            <option value="current">Saat ini</option>
                                                            <option value="01">Januari</option>
                                                            <option value="02">Februari</option>
                                                            <option value="03">Maret</option>
                                                            <option value="04">April</option>
                                                            <option value="05">Mei</option>
                                                            <option value="06">Juni</option>
                                                            <option value="07">Juli</option>
                                                            <option value="08">Agustus</option>
                                                            <option value="09">September</option>
                                                            <option value="10">Oktober</option>
                                                            <option value="11">November</option>
                                                            <option value="12">Desember</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label">&nbsp;</label>
                                                        <select class="form-select" name="custom_edu_end_year[]">
                                                            <option value="">Tahun</option>
                                                            @for($year = date('Y'); $year >= 1990; $year--)
                                                                <option value="{{ $year }}">{{ $year }}</option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Deskripsi (Opsional)</label>
                                                <textarea class="form-control" 
                                                          name="custom_edu_description[]" 
                                                          rows="2"
                                                          placeholder="Keterangan tambahan tentang pendidikan..."></textarea>
                                            </div>
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-education" style="display: none;">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="add-education">
                                        <i class="fas fa-plus me-1"></i> Tambah Pendidikan
                                    </button>
                                </div>
                            </div>

                            <!-- Keahlian Section -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="fas fa-cogs me-2"></i>
                                        Keahlian
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="custom_skills" class="form-label">Keahlian</label>
                                        <textarea class="form-control" 
                                                  id="custom_skills" 
                                                  name="custom_skills" 
                                                  rows="4" 
                                                  placeholder="Contoh: Microsoft Office, Adobe Photoshop, Programming (PHP, JavaScript), Database Management, Networking">{{ old('custom_skills') }}</textarea>
                                        <small class="form-text text-muted">Pisahkan setiap keahlian dengan koma (,)</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Referensi Section -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="fas fa-users me-2"></i>
                                        Referensi
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="references_available" name="references_available" value="1">
                                            <label class="form-check-label" for="references_available">
                                                Referensi tersedia atas permintaan
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div id="reference-details" style="display: none;">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Nama Referensi 1</label>
                                                    <input type="text" class="form-control" name="custom_ref_name_1" placeholder="Nama lengkap">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Jabatan/Posisi</label>
                                                    <input type="text" class="form-control" name="custom_ref_position_1" placeholder="Manager, Supervisor, dll">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Nomor Telepon</label>
                                                    <input type="text" class="form-control" name="custom_ref_phone_1" placeholder="+62 812 3456 7890">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Nama Referensi 2</label>
                                                    <input type="text" class="form-control" name="custom_ref_name_2" placeholder="Nama lengkap">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Jabatan/Posisi</label>
                                                    <input type="text" class="form-control" name="custom_ref_position_2" placeholder="Manager, Supervisor, dll">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Nomor Telepon</label>
                                                    <input type="text" class="form-control" name="custom_ref_phone_2" placeholder="+62 812 3456 7890">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="set_as_default" 
                                       name="set_as_default" 
                                       value="1" 
                                       {{ old('set_as_default') ? 'checked' : '' }}>
                                <label class="form-check-label" for="set_as_default">
                                    <i class="fas fa-star me-1"></i>
                                    Set sebagai CV Default
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('alumni.cv.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>
                                Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-file-pdf me-1"></i>
                                Buat CV
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
    const profileRadio = document.getElementById('profile_data');
    const customRadio = document.getElementById('custom_data');
    const customForm = document.getElementById('customDataForm');
    
    function toggleCustomForm() {
        if (customRadio.checked) {
            customForm.style.display = 'block';
            // Make custom fields required
            const customFields = customForm.querySelectorAll('input[name="custom_name"], input[name="custom_email"]');
            customFields.forEach(field => {
                field.required = true;
            });
        } else {
            customForm.style.display = 'none';
            // Remove required from custom fields
            const customFields = customForm.querySelectorAll('input, textarea');
            customFields.forEach(field => {
                field.required = false;
            });
        }
    }
    
    profileRadio.addEventListener('change', toggleCustomForm);
    customRadio.addEventListener('change', toggleCustomForm);
    
    // Initial state
    toggleCustomForm();
    
    // Add Experience functionality
    let experienceCount = 1;
    document.getElementById('add-experience').addEventListener('click', function() {
        experienceCount++;
        const container = document.getElementById('experience-container');
        const template = container.querySelector('.experience-item').cloneNode(true);
        
        // Clear values
        template.querySelectorAll('input, textarea, select').forEach(field => {
            field.value = '';
        });
        
        // Show remove button
        template.querySelector('.remove-experience').style.display = 'inline-block';
        
        container.appendChild(template);
        updateRemoveButtons();
    });

    // Add Education functionality
    let educationCount = 1;
    document.getElementById('add-education').addEventListener('click', function() {
        educationCount++;
        const container = document.getElementById('education-container');
        const template = container.querySelector('.education-item').cloneNode(true);
        
        // Clear values
        template.querySelectorAll('input, textarea, select').forEach(field => {
            field.value = '';
        });
        
        // Show remove button
        template.querySelector('.remove-education').style.display = 'inline-block';
        
        container.appendChild(template);
        updateRemoveButtons();
    });

    // Remove experience/education functionality
    function updateRemoveButtons() {
        // Experience remove buttons
        document.querySelectorAll('.remove-experience').forEach(button => {
            button.addEventListener('click', function() {
                if (document.querySelectorAll('.experience-item').length > 1) {
                    this.closest('.experience-item').remove();
                    updateRemoveButtons();
                }
            });
        });

        // Education remove buttons
        document.querySelectorAll('.remove-education').forEach(button => {
            button.addEventListener('click', function() {
                if (document.querySelectorAll('.education-item').length > 1) {
                    this.closest('.education-item').remove();
                    updateRemoveButtons();
                }
            });
        });

        // Show/hide remove buttons based on count
        const experienceItems = document.querySelectorAll('.experience-item');
        const educationItems = document.querySelectorAll('.education-item');

        experienceItems.forEach((item, index) => {
            const removeBtn = item.querySelector('.remove-experience');
            if (experienceItems.length > 1) {
                removeBtn.style.display = 'inline-block';
            } else {
                removeBtn.style.display = 'none';
            }
        });

        educationItems.forEach((item, index) => {
            const removeBtn = item.querySelector('.remove-education');
            if (educationItems.length > 1) {
                removeBtn.style.display = 'inline-block';
            } else {
                removeBtn.style.display = 'none';
            }
        });
    }

    // References toggle
    const referencesCheckbox = document.getElementById('references_available');
    const referenceDetails = document.getElementById('reference-details');
    
    referencesCheckbox.addEventListener('change', function() {
        if (this.checked) {
            referenceDetails.style.display = 'block';
        } else {
            referenceDetails.style.display = 'none';
        }
    });
    
    // Form validation
    const form = document.getElementById('cvForm');
    form.addEventListener('submit', function(e) {
        const title = document.getElementById('title').value.trim();
        const template = document.getElementById('template').value;
        const dataSource = document.querySelector('input[name="data_source"]:checked');
        
        if (!title) {
            e.preventDefault();
            alert('Judul CV harus diisi');
            return;
        }
        
        if (!template) {
            e.preventDefault();
            alert('Template harus dipilih');
            return;
        }
        
        if (!dataSource) {
            e.preventDefault();
            alert('Sumber data harus dipilih');
            return;
        }
        
        if (dataSource.value === 'custom') {
            const customName = document.getElementById('custom_name').value.trim();
            const customEmail = document.getElementById('custom_email').value.trim();
            
            if (!customName || !customEmail) {
                e.preventDefault();
                alert('Nama depan dan email harus diisi untuk data manual');
                return;
            }
        }
    });

    // Initialize remove buttons
    updateRemoveButtons();
});
</script>
@endpush 