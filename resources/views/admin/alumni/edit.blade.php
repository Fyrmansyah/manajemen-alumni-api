@extends('layouts.app')

@section('title', 'Edit Alumni - Admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-user-edit text-primary me-2"></i>
                        Edit Alumni
                    </h1>
                    <p class="text-muted mb-0">Perbarui data alumni {{ $alumni->nama_lengkap }}</p>
                </div>
                <a href="{{ route('admin.alumni.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>
                    Kembali
                </a>
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
                        <i class="fas fa-edit me-2"></i>
                        Form Edit Alumni
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.alumni.update', $alumni) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Data Pribadi -->
                            <div class="col-lg-6">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-user me-1"></i>
                                    Data Pribadi
                                </h6>
                                
                                <div class="mb-3">
                                    <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror" 
                                           id="nama_lengkap" name="nama_lengkap" 
                                           value="{{ old('nama_lengkap', $alumni->nama_lengkap) }}" 
                                           placeholder="Masukkan nama lengkap">
                                    @error('nama_lengkap')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="nisn" class="form-label">NISN <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nisn') is-invalid @enderror" 
                                           id="nisn" name="nisn" 
                                           value="{{ old('nisn', $alumni->nisn) }}" 
                                           placeholder="Masukkan NISN">
                                    @error('nisn')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" 
                                           value="{{ old('email', $alumni->email) }}" 
                                           placeholder="Masukkan email">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="no_hp" class="form-label">No. HP</label>
                                    <input type="text" class="form-control @error('no_hp') is-invalid @enderror" 
                                           id="no_hp" name="no_hp" 
                                           value="{{ old('no_hp', $alumni->no_hp) }}" 
                                           placeholder="Masukkan nomor HP">
                                    @error('no_hp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="alamat" class="form-label">Alamat</label>
                                    <textarea class="form-control @error('alamat') is-invalid @enderror" 
                                              id="alamat" name="alamat" rows="3" 
                                              placeholder="Masukkan alamat lengkap">{{ old('alamat', $alumni->alamat) }}</textarea>
                                    @error('alamat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Data Akademik -->
                            <div class="col-lg-6">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-graduation-cap me-1"></i>
                                    Data Akademik & Pekerjaan
                                </h6>

                                <div class="mb-3">
                                    <label for="jurusan_id" class="form-label">Jurusan <span class="text-danger">*</span></label>
                                    <select class="form-select @error('jurusan_id') is-invalid @enderror" 
                                            id="jurusan_id" name="jurusan_id">
                                        <option value="">Pilih Jurusan</option>
                                        @foreach($jurusans as $jurusan)
                                            <option value="{{ $jurusan->id }}" 
                                                    {{ old('jurusan_id', $alumni->jurusan_id) == $jurusan->id ? 'selected' : '' }}>
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
                                            id="tahun_lulus" name="tahun_lulus">
                                        <option value="">Pilih Tahun Lulus</option>
                                        @for($year = date('Y') + 5; $year >= 2000; $year--)
                                            <option value="{{ $year }}" 
                                                    {{ old('tahun_lulus', $alumni->tahun_lulus) == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('tahun_lulus')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="status_kerja" class="form-label">Status Kerja</label>
                                    <select class="form-select @error('status_kerja') is-invalid @enderror" 
                                            id="status_kerja" name="status_kerja">
                                        <option value="">Pilih Status</option>
                                        <option value="bekerja" {{ old('status_kerja', $alumni->status_kerja) == 'bekerja' ? 'selected' : '' }}>Bekerja</option>
                                        <option value="kuliah" {{ old('status_kerja', $alumni->status_kerja) == 'kuliah' ? 'selected' : '' }}>Kuliah</option>
                                        <option value="wirausaha" {{ old('status_kerja', $alumni->status_kerja) == 'wirausaha' ? 'selected' : '' }}>Wirausaha</option>
                                        <option value="menganggur" {{ old('status_kerja', $alumni->status_kerja) == 'menganggur' ? 'selected' : '' }}>Menganggur</option>
                                    </select>
                                    @error('status_kerja')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3" id="perusahaanGroup" style="display: none;">
                                    <label for="perusahaan" class="form-label">Perusahaan</label>
                                    <input type="text" class="form-control @error('perusahaan') is-invalid @enderror" 
                                           id="perusahaan" name="perusahaan" 
                                           value="{{ old('perusahaan', $alumni->perusahaan) }}" 
                                           placeholder="Nama perusahaan">
                                    @error('perusahaan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3" id="posisiGroup" style="display: none;">
                                    <label for="posisi" class="form-label">Posisi/Jabatan</label>
                                    <input type="text" class="form-control @error('posisi') is-invalid @enderror" 
                                           id="posisi" name="posisi" 
                                           value="{{ old('posisi', $alumni->posisi) }}" 
                                           placeholder="Posisi atau jabatan">
                                    @error('posisi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3" id="gajiGroup" style="display: none;">
                                    <label for="gaji" class="form-label">Gaji (Opsional)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control @error('gaji') is-invalid @enderror" 
                                               id="gaji" name="gaji" 
                                               value="{{ old('gaji', $alumni->gaji) }}" 
                                               placeholder="0">
                                    </div>
                                    @error('gaji')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Kuliah fields -->
                                <div class="mb-3" id="kuliahGroup" style="display: none;">
                                    <label class="form-label">Informasi Kuliah</label>
                                    <div class="mb-2">
                                        <label for="tempat_kuliah" class="form-label">Nama Kampus/Universitas</label>
                                        <input type="text" class="form-control @error('tempat_kuliah') is-invalid @enderror"
                                               id="tempat_kuliah" name="tempat_kuliah"
                                               value="{{ old('tempat_kuliah', $alumni->tempat_kuliah) }}"
                                               placeholder="Misal: Universitas Indonesia">
                                        @error('tempat_kuliah')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="prodi_kuliah" class="form-label">Program Studi</label>
                                        <input type="text" class="form-control @error('prodi_kuliah') is-invalid @enderror"
                                               id="prodi_kuliah" name="prodi_kuliah"
                                               value="{{ old('prodi_kuliah', $alumni->prodi_kuliah) }}"
                                               placeholder="Misal: Informatika">
                                        @error('prodi_kuliah')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="is_verified" class="form-label">Status Verifikasi</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               id="is_verified" name="is_verified" value="1"
                                               {{ old('is_verified', $alumni->is_verified) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_verified">
                                            Alumni sudah terverifikasi
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <hr>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.alumni.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i>
                                        Batal
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>
                                        Update Alumni
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
    const statusKerjaSelect = document.getElementById('status_kerja');
    const perusahaanGroup = document.getElementById('perusahaanGroup');
    const posisiGroup = document.getElementById('posisiGroup');
    const gajiGroup = document.getElementById('gajiGroup');
    const kuliahGroup = document.getElementById('kuliahGroup');
    
    function toggleWorkFields() {
        const status = statusKerjaSelect.value;
        
        if (status === 'bekerja' || status === 'wirausaha') {
            perusahaanGroup.style.display = 'block';
            posisiGroup.style.display = 'block';
            gajiGroup.style.display = 'block';
            kuliahGroup.style.display = 'none';
        } else if (status === 'kuliah') {
            perusahaanGroup.style.display = 'none';
            posisiGroup.style.display = 'none';
            gajiGroup.style.display = 'none';
            kuliahGroup.style.display = 'block';
        } else {
            perusahaanGroup.style.display = 'none';
            posisiGroup.style.display = 'none';
            gajiGroup.style.display = 'none';
            kuliahGroup.style.display = 'none';
        }
    }
    
    // Initialize fields based on current status
    toggleWorkFields();
    
    // Add event listener for status change
    statusKerjaSelect.addEventListener('change', toggleWorkFields);
    
    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const namaLengkap = document.getElementById('nama_lengkap').value.trim();
        const nisn = document.getElementById('nisn').value.trim();
        const email = document.getElementById('email').value.trim();
        const jurusanId = document.getElementById('jurusan_id').value;
        const tahunLulus = document.getElementById('tahun_lulus').value;
        
        if (!namaLengkap || !nisn || !email || !jurusanId || !tahunLulus) {
            e.preventDefault();
            alert('Harap lengkapi semua field yang wajib diisi!');
            return false;
        }
        
        // NISN validation
        if (nisn.length !== 10 || !/^\d+$/.test(nisn)) {
            e.preventDefault();
            alert('NISN harus berupa 10 digit angka!');
            return false;
        }
        
        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            e.preventDefault();
            alert('Format email tidak valid!');
            return false;
        }
        
        return true;
    });
});
</script>
@endpush
