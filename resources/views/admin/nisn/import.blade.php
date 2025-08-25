@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4 mb-5">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">Import NISN</h1>
                <p class="text-muted mb-0">Unggah file CSV data master NISN</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
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
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-file-upload me-2"></i>Upload File CSV</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.nisn.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">File CSV</label>
                            <input type="file" name="file" accept=".csv" required class="form-control" />
                            <small class="text-muted">Format: satu NISN per baris, hanya digit. Baris tidak valid akan dilewati.</small>
                        </div>
                        <button class="btn btn-primary"><i class="fas fa-upload me-1"></i> Upload & Import</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-info-circle me-2"></i>Panduan</h6>
                </div>
                <div class="card-body">
                    <ol class="mb-3">
                        <li>Siapkan file <code>.csv</code> dengan satu kolom berisi daftar NISN.</li>
                        <li>Pastikan hanya karakter digit (tanpa spasi / tanda kutip).</li>
                        <li>Duplikat otomatis dilewati, hanya yang belum ada ditambahkan.</li>
                        <li>Gunakan import ulang untuk menambah batch baru (idempotent).</li>
                    </ol>
                    <p class="mb-1"><strong>Contoh isi file:</strong></p>
<pre class="bg-light p-2 border"><code>1234567890
1234567891
1234567892</code></pre>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
