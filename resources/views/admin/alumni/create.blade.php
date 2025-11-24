@extends('layouts.app')

@section('title', 'Tambah Alumni - Admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-user-plus text-primary me-2"></i>
                        Tambah Alumni
                    </h1>
                    <p class="text-muted mb-0">Tambahkan data alumni baru ke sistem</p>
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
                <div class="card-body">
                    <form action="{{ route('admin.alumni.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Personal Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="card-title mb-3">
                                    <i class="fas fa-user me-2"></i>Informasi Personal
                                </h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nisn" class="form-label">NISN <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('nisn') is-invalid @enderror" 
                                           id="nisn" 
                                           name="nisn" 
                                           value="{{ old('nisn') }}" 
                                           placeholder="Masukkan NISN">
                                    @error('nisn')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                     <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('nama') is-invalid @enderror" 
                                           id="nama" 
                         name="nama" required
                         value="{{ old('nama') }}" 
                                           placeholder="Masukkan nama lengkap">
                                    @error('nama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                    <select class="form-select @error('jenis_kelamin') is-invalid @enderror" 
                                            id="jenis_kelamin" 
                        name="jenis_kelamin" required>
                                        <option value="">Pilih jenis kelamin</option>
                                        <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                    @error('jenis_kelamin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tgl_lahir" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                    <input type="date" required
                                           class="form-control @error('tgl_lahir') is-invalid @enderror" 
                                           id="tgl_lahir" 
                                           name="tgl_lahir" 
                                           value="{{ old('tgl_lahir') }}">
                                    @error('tgl_lahir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tempat_lahir" class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                                    <input type="text" required
                                           class="form-control @error('tempat_lahir') is-invalid @enderror" 
                                           id="tempat_lahir" 
                                           name="tempat_lahir" 
                                           value="{{ old('tempat_lahir') }}" 
                                           placeholder="Masukkan tempat lahir">
                                    @error('tempat_lahir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" required
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           placeholder="contoh@email.com">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                    <input type="password" required
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Minimal 6 karakter">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="no_tlp" class="form-label">No. Telepon <span class="text-danger">*</span></label>
                                    <input type="text" required
                                           class="form-control @error('no_tlp') is-invalid @enderror" 
                                           id="no_tlp" 
                                           name="no_tlp" 
                                           value="{{ old('no_tlp') }}" 
                                           placeholder="08xx-xxxx-xxxx">
                                    @error('no_tlp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('alamat') is-invalid @enderror" required
                                              id="alamat" 
                                              name="alamat" 
                                              rows="3" 
                                              placeholder="Masukkan alamat lengkap">{{ old('alamat') }}</textarea>
                                    @error('alamat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="alamat_rt" class="form-label">RT</label>
                                    <input type="text" 
                                           class="form-control @error('alamat_rt') is-invalid @enderror" 
                                           id="alamat_rt" 
                                           name="alamat_rt" 
                                           value="{{ old('alamat_rt') }}" 
                                           placeholder="001">
                                    @error('alamat_rt')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="alamat_rw" class="form-label">RW</label>
                                    <input type="text" 
                                           class="form-control @error('alamat_rw') is-invalid @enderror" 
                                           id="alamat_rw" 
                                           name="alamat_rw" 
                                           value="{{ old('alamat_rw') }}" 
                                           placeholder="001">
                                    @error('alamat_rw')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="alamat_kelurahan" class="form-label">Kelurahan / Desa</label>
                                    <input type="text"
                                           class="form-control @error('alamat_kelurahan') is-invalid @enderror"
                                           id="alamat_kelurahan"
                                           name="alamat_kelurahan"
                                           value="{{ old('alamat_kelurahan') }}"
                                           placeholder="Masukkan kelurahan atau desa">
                                    @error('alamat_kelurahan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="alamat_kecamatan" class="form-label">Kecamatan</label>
                                    <input type="text" 
                                           class="form-control @error('alamat_kecamatan') is-invalid @enderror" 
                                           id="alamat_kecamatan" 
                                           name="alamat_kecamatan" 
                                           value="{{ old('alamat_kecamatan') }}" 
                                           placeholder="Masukkan kecamatan">
                                    @error('alamat_kecamatan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="alamat_kode_pos" class="form-label">Kode Pos</label>
                                    <input type="text" 
                                           class="form-control @error('alamat_kode_pos') is-invalid @enderror" 
                                           id="alamat_kode_pos" 
                                           name="alamat_kode_pos" 
                                           value="{{ old('alamat_kode_pos') }}" 
                                           placeholder="60000">
                                    @error('alamat_kode_pos')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Academic Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="card-title mb-3">
                                    <i class="fas fa-graduation-cap me-2"></i>Informasi Akademik
                                </h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="jurusan_id" class="form-label">Jurusan <span class="text-danger">*</span></label>
                    <select class="form-select @error('jurusan_id') is-invalid @enderror" 
                                            id="jurusan_id" 
                        name="jurusan_id" required>
                                        <option value="">Pilih jurusan</option>
                                        @foreach(\App\Models\Jurusan::all() as $jurusan)
                                            <option value="{{ $jurusan->id }}" {{ old('jurusan_id') == $jurusan->id ? 'selected' : '' }}>
                                                {{ $jurusan->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('jurusan_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="tahun_mulai" class="form-label">Tahun Mulai <span class="text-danger">*</span></label>
                                    <input type="number" required
                                           class="form-control @error('tahun_mulai') is-invalid @enderror" 
                                           id="tahun_mulai" 
                                           name="tahun_mulai" 
                                           value="{{ old('tahun_mulai') }}" 
                                           min="1900" 
                                           max="{{ date('Y') + 10 }}" 
                                           placeholder="{{ date('Y') }}">
                                    @error('tahun_mulai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="tahun_lulus" class="form-label">Tahun Lulus <span class="text-danger">*</span></label>
                                    <input type="number" required
                                           class="form-control @error('tahun_lulus') is-invalid @enderror" 
                                           id="tahun_lulus" 
                                           name="tahun_lulus" 
                                           value="{{ old('tahun_lulus') }}" 
                                           min="1900" 
                                           max="{{ date('Y') + 10 }}" 
                                           placeholder="{{ date('Y') }}">
                                    @error('tahun_lulus')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Career Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="card-title mb-3">
                                    <i class="fas fa-briefcase me-2"></i>Informasi Karir (Opsional)
                                </h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tempat_kerja" class="form-label">Tempat Kerja</label>
                                    <input type="text" 
                                           class="form-control @error('tempat_kerja') is-invalid @enderror" 
                                           id="tempat_kerja" 
                                           name="tempat_kerja" 
                                           value="{{ old('tempat_kerja') }}" 
                                           placeholder="Nama perusahaan">
                                    @error('tempat_kerja')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="jabatan_kerja" class="form-label">Jabatan</label>
                                    <input type="text" 
                                           class="form-control @error('jabatan_kerja') is-invalid @enderror" 
                                           id="jabatan_kerja" 
                                           name="jabatan_kerja" 
                                           value="{{ old('jabatan_kerja') }}" 
                                           placeholder="Posisi/jabatan">
                                    @error('jabatan_kerja')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tempat_kuliah" class="form-label">Tempat Kuliah</label>
                                    <input type="text" 
                                           class="form-control @error('tempat_kuliah') is-invalid @enderror" 
                                           id="tempat_kuliah" 
                                           name="tempat_kuliah" 
                                           value="{{ old('tempat_kuliah') }}" 
                                           placeholder="Nama universitas/institusi">
                                    @error('tempat_kuliah')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="prodi_kuliah" class="form-label">Program Studi</label>
                                    <input type="text" 
                                           class="form-control @error('prodi_kuliah') is-invalid @enderror" 
                                           id="prodi_kuliah" 
                                           name="prodi_kuliah" 
                                           value="{{ old('prodi_kuliah') }}" 
                                           placeholder="Nama program studi">
                                    @error('prodi_kuliah')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Kesesuaian Kerja</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="kesesuaian_kerja" id="kerja_sesuai" value="1" {{ old('kesesuaian_kerja') == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="kerja_sesuai">
                                            Sesuai
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="kesesuaian_kerja" id="kerja_tidak_sesuai" value="0" {{ old('kesesuaian_kerja') == '0' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="kerja_tidak_sesuai">
                                            Tidak Sesuai
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Kesesuaian Kuliah</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="kesesuaian_kuliah" id="kuliah_sesuai" value="1" {{ old('kesesuaian_kuliah') == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="kuliah_sesuai">
                                            Sesuai
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="kesesuaian_kuliah" id="kuliah_tidak_sesuai" value="0" {{ old('kesesuaian_kuliah') == '0' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="kuliah_tidak_sesuai">
                                            Tidak Sesuai
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="card-title mb-3">
                                    <i class="fas fa-info-circle me-2"></i>Informasi Tambahan
                                </h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="pengalaman_kerja" class="form-label">Pengalaman Kerja</label>
                                    <textarea class="form-control @error('pengalaman_kerja') is-invalid @enderror" 
                                              id="pengalaman_kerja" 
                                              name="pengalaman_kerja" 
                                              rows="3" 
                                              placeholder="Deskripsikan pengalaman kerja">{{ old('pengalaman_kerja') }}</textarea>
                                    @error('pengalaman_kerja')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="keahlian" class="form-label">Keahlian</label>
                                    <textarea class="form-control @error('keahlian') is-invalid @enderror" 
                                              id="keahlian" 
                                              name="keahlian" 
                                              rows="3" 
                                              placeholder="Daftar keahlian yang dimiliki">{{ old('keahlian') }}</textarea>
                                    @error('keahlian')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="foto" class="form-label">Foto Profil</label>
                                    <input type="file" 
                                           class="form-control @error('foto') is-invalid @enderror" 
                                           id="foto" 
                                           name="foto" 
                                           accept="image/*">
                                    <small class="text-muted">Format: JPG, PNG, GIF. Maksimal 2MB</small>
                                    @error('foto')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" name="is_verified" id="is_verified" value="1" {{ old('is_verified') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_verified">
                                            Alumni sudah terverifikasi
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <hr>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.alumni.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                Simpan Alumni
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
.card {
    border-radius: 12px;
}
.form-control, .form-select {
    border-radius: 8px;
}
.btn {
    border-radius: 8px;
}
</style>
@endpush
