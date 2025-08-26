@extends('layouts.app')

@section('title','Edit Perusahaan')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Perusahaan</h4>
        <a href="{{ route('admin.companies.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Kembali</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 small">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.companies.update',$company) }}" class="row g-3">
                @csrf
                @method('PUT')
                <div class="col-md-6">
                    <label class="form-label">Nama Perusahaan <span class="text-danger">*</span></label>
                    <input type="text" name="company_name" value="{{ old('company_name',$company->company_name) }}" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" value="{{ old('email',$company->email) }}" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone',$company->phone) }}" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Website</label>
                    <input type="url" name="website" value="{{ old('website',$company->website) }}" class="form-control" placeholder="https://...">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Industri</label>
                    <input type="text" name="industry" value="{{ old('industry',$company->industry) }}" class="form-control">
                </div>
                <div class="col-12">
                    <label class="form-label">Alamat</label>
                    <textarea name="address" class="form-control" rows="2">{{ old('address',$company->address) }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control" rows="4">{{ old('description',$company->description) }}</textarea>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        @foreach(['active'=>'Aktif','pending'=>'Pending','inactive'=>'Tidak Aktif'] as $k=>$v)
                            <option value="{{ $k }}" {{ old('status',$company->status)==$k?'selected':'' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <button class="btn btn-primary"><i class="fas fa-save me-1"></i>Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
