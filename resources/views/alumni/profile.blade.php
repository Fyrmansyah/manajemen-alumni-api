@extends('layouts.app')

@section('title', 'Profil Alumni - BKK SMKN 1 Surabaya')

@section('content')
<div class="container-fluid mt-4 mb-5">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Profil Alumni</h1>
                    <p class="text-muted mb-0">Kelola informasi profil Anda</p>
                </div>
                <div>
                    <span class="badge bg-primary">Profil {{ auth('alumni')->user()->profile_completion }}% Lengkap</span>
                    <small class="text-muted ms-2">{{ now()->format('d M Y, H:i') }} WIB</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Profile Information -->
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-user-edit me-2"></i>Informasi Profil
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('alumni.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <!-- Profile Completion Progress -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Kelengkapan Profil</span>
                                <span class="text-muted">{{ auth('alumni')->user()->profile_completion }}%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-{{ auth('alumni')->user()->profile_completion >= 80 ? 'success' : (auth('alumni')->user()->profile_completion >= 60 ? 'warning' : 'danger') }}" 
                                     role="progressbar" 
                                     style="width: {{ auth('alumni')->user()->profile_completion }}%"></div>
                            </div>
                            <small class="text-muted">
                                @if(auth('alumni')->user()->profile_completion < 80)
                                    <i class="fas fa-exclamation-triangle text-warning me-1"></i>
                                    Lengkapi profil Anda untuk dapat melamar pekerjaan
                                @else
                                    <i class="fas fa-check-circle text-success me-1"></i>
                                    Profil Anda sudah lengkap untuk melamar pekerjaan
                                @endif
                            </small>
                        </div>

                        <div class="row">
                            <!-- Personal Information -->
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3">Informasi Pribadi</h6>
                                <div class="mb-3">
                                    <label for="foto" class="form-label">Foto Profil</label>
                                    <input type="file" class="form-control @error('foto') is-invalid @enderror" id="foto" name="foto" accept="image/*">
                                    @error('foto')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Format: JPG/PNG, maks 2MB.</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror" 
                                           id="nama_lengkap" name="nama_lengkap" 
                                           value="{{ old('nama_lengkap', auth('alumni')->user()->nama_lengkap ?: auth('alumni')->user()->nama) }}" required>
                                    @error('nama_lengkap')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" 
                                           value="{{ old('email', auth('alumni')->user()->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="phone" class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" 
                                           value="{{ old('phone', auth('alumni')->user()->phone ?: auth('alumni')->user()->no_tlp) }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="tanggal_lahir" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror" 
                                           id="tanggal_lahir" name="tanggal_lahir" 
                                           value="{{ old('tanggal_lahir', auth('alumni')->user()->tanggal_lahir ?: auth('alumni')->user()->tgl_lahir) }}" required>
                                    @error('tanggal_lahir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                    <select class="form-select @error('jenis_kelamin') is-invalid @enderror" 
                                            id="jenis_kelamin" name="jenis_kelamin" required>
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="L" {{ old('jenis_kelamin', auth('alumni')->user()->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="P" {{ old('jenis_kelamin', auth('alumni')->user()->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                    @error('jenis_kelamin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Academic Information -->
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3">Informasi Akademik</h6>
                                
                                <div class="mb-3">
                                    <label for="nisn" class="form-label">NISN <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nisn') is-invalid @enderror" 
                                           id="nisn" name="nisn" 
                                           value="{{ old('nisn', auth('alumni')->user()->nisn) }}" 
                                           placeholder="Masukkan NISN Anda" required>
                                    @error('nisn')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="jurusan_id" class="form-label">Jurusan <span class="text-danger">*</span></label>
                                    <select class="form-select @error('jurusan_id') is-invalid @enderror" 
                                            id="jurusan_id" name="jurusan_id" required>
                                        <option value="">Pilih Jurusan</option>
                                        @foreach($jurusans as $jurusan)
                                            <option value="{{ $jurusan->id }}" 
                                                {{ old('jurusan_id', auth('alumni')->user()->jurusan_id) == $jurusan->id ? 'selected' : '' }}>
                                                {{ $jurusan->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('jurusan_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="tahun_lulus" class="form-label">Tahun Lulus <span class="text-danger">*</span></label>
                                    <select class="form-select @error('tahun_lulus') is-invalid @enderror" 
                                            id="tahun_lulus" name="tahun_lulus" required>
                                        <option value="">Pilih Tahun Lulus</option>
                                        @for($year = date('Y'); $year >= date('Y') - 10; $year--)
                                            <option value="{{ $year }}" 
                                                {{ old('tahun_lulus', auth('alumni')->user()->tahun_lulus) == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('tahun_lulus')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Informasi Kuliah (opsional) -->
                                <div class="mb-3">
                                    <label for="tempat_kuliah" class="form-label">Nama Kampus/Universitas</label>
                                    <input type="text" class="form-control @error('tempat_kuliah') is-invalid @enderror"
                                           id="tempat_kuliah" name="tempat_kuliah"
                                           value="{{ old('tempat_kuliah', auth('alumni')->user()->tempat_kuliah) }}"
                                           placeholder="Contoh: Universitas Negeri Surabaya">
                                    @error('tempat_kuliah')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Isi jika Anda sedang/ Pernah kuliah.</div>
                                </div>

                                <div class="mb-3">
                                    <label for="prodi_kuliah" class="form-label">Program Studi</label>
                                    <input type="text" class="form-control @error('prodi_kuliah') is-invalid @enderror"
                                           id="prodi_kuliah" name="prodi_kuliah"
                                           value="{{ old('prodi_kuliah', auth('alumni')->user()->prodi_kuliah) }}"
                                           placeholder="Contoh: Teknik Informatika">
                                    @error('prodi_kuliah')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('alamat') is-invalid @enderror" 
                                              id="alamat" name="alamat" rows="3" required>{{ old('alamat', auth('alumni')->user()->alamat) }}</textarea>
                                    @error('alamat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Professional Information -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">Informasi Profesional</h6>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="pengalaman_kerja" class="form-label">Pengalaman Kerja</label>
                                    <textarea class="form-control @error('pengalaman_kerja') is-invalid @enderror" 
                                              id="pengalaman_kerja" name="pengalaman_kerja" rows="3" 
                                              placeholder="Jelaskan pengalaman kerja Anda (opsional)">{{ old('pengalaman_kerja', auth('alumni')->user()->pengalaman_kerja) }}</textarea>
                                    <div class="form-text">Contoh: Saya pernah bekerja sebagai IT Support di PT ABC selama 2 tahun</div>
                                    @error('pengalaman_kerja')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="keahlian" class="form-label">Keahlian</label>
                                    <textarea class="form-control @error('keahlian') is-invalid @enderror" 
                                              id="keahlian" name="keahlian" rows="3" 
                                              placeholder="Jelaskan keahlian Anda (opsional)">{{ old('keahlian', auth('alumni')->user()->keahlian) }}</textarea>
                                    <div class="form-text">Contoh: Microsoft Office, Adobe Photoshop, Programming (PHP, JavaScript), Networking</div>
                                    @error('keahlian')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- WhatsApp Notification Preferences -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">Preferensi Notifikasi</h6>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="whatsapp_notifications" 
                                               name="whatsapp_notifications" value="1" 
                                               {{ old('whatsapp_notifications', auth('alumni')->user()->whatsapp_notifications) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="whatsapp_notifications">
                                            <i class="fab fa-whatsapp text-success me-2"></i>
                                            Terima notifikasi WhatsApp untuk lowongan kerja baru
                                        </label>
                                    </div>
                                    <small class="text-muted">Anda akan menerima notifikasi lowongan kerja yang sesuai dengan profil Anda</small>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('alumni.dashboard') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Simpan Perubahan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Profile Summary -->
        <div class="col-lg-4">
            <!-- Profile Card -->
            <div class="card shadow mb-4">
                <div class="card-body text-center">
                    <div class="mb-3">
                        @if(auth('alumni')->user()->foto)
                            <img src="{{ asset('storage/' . (\Illuminate\Support\Str::startsWith(auth('alumni')->user()->foto, 'alumni_photos/') ? auth('alumni')->user()->foto : ('alumni_photos/' . ltrim(auth('alumni')->user()->foto, '/'))) ) }}" 
                                 alt="Foto Profil" class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                        @else
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" 
                                 style="width: 120px; height: 120px;">
                                <i class="fas fa-user fa-3x text-muted"></i>
                            </div>
                        @endif
                    </div>
                    
                    <h5 class="mb-1">{{ auth('alumni')->user()->nama_lengkap ?: auth('alumni')->user()->nama ?: 'Nama Belum Diisi' }}</h5>
                    <p class="text-muted mb-2">
                        @if(auth('alumni')->user()->jurusan)
                            {{ auth('alumni')->user()->jurusan->nama }}
                        @else
                            Jurusan Belum Dipilih
                        @endif
                    </p>
                    
                    <div class="d-flex justify-content-center mb-3">
                        <span class="badge bg-{{ auth('alumni')->user()->profile_completion >= 80 ? 'success' : (auth('alumni')->user()->profile_completion >= 60 ? 'warning' : 'danger') }}">
                            {{ auth('alumni')->user()->profile_completion }}% Lengkap
                        </span>
                    </div>
                    
                    <div class="row text-center">
                        <div class="col-6">
                            <h6 class="mb-1">{{ auth('alumni')->user()->applications()->count() }}</h6>
                            <small class="text-muted">Total Lamaran</small>
                        </div>
                        <div class="col-6">
                            <h6 class="mb-1">{{ auth('alumni')->user()->applications()->where('status', 'accepted')->count() }}</h6>
                            <small class="text-muted">Diterima</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Statistik Lamaran
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Menunggu</span>
                            <span class="text-muted">{{ auth('alumni')->user()->applications()->where('status', 'submitted')->count() }}</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-warning" style="width: {{ auth('alumni')->user()->applications()->where('status', 'submitted')->count() > 0 ? '100' : '0' }}%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Direview</span>
                            <span class="text-muted">{{ auth('alumni')->user()->applications()->where('status', 'reviewed')->count() }}</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-info" style="width: {{ auth('alumni')->user()->applications()->where('status', 'reviewed')->count() > 0 ? '100' : '0' }}%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Interview</span>
                            <span class="text-muted">{{ auth('alumni')->user()->applications()->where('status', 'interview')->count() }}</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-warning" style="width: {{ auth('alumni')->user()->applications()->where('status', 'interview')->count() > 0 ? '100' : '0' }}%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Diterima</span>
                            <span class="text-muted">{{ auth('alumni')->user()->applications()->where('status', 'accepted')->count() }}</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: {{ auth('alumni')->user()->applications()->where('status', 'accepted')->count() > 0 ? '100' : '0' }}%"></div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Ditolak</span>
                            <span class="text-muted">{{ auth('alumni')->user()->applications()->where('status', 'rejected')->count() }}</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-danger" style="width: {{ auth('alumni')->user()->applications()->where('status', 'rejected')->count() > 0 ? '100' : '0' }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tips -->
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-lightbulb me-2"></i>Tips Profil Lengkap
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Isi semua informasi wajib (*)
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Tambahkan pengalaman kerja jika ada
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Jelaskan keahlian yang dimiliki
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Aktifkan notifikasi WhatsApp
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success me-2"></i>
                            Update profil secara berkala
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(function(field) {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Silakan lengkapi semua field yang wajib diisi');
        }
    });
});
</script>
@endpush

@section('styles')
<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.text-xs {
    font-size: 0.7rem;
}

.card {
    border: 0;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.progress {
    border-radius: 0.35rem;
}

.form-control:focus,
.form-select:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}
</style>
@endsection 