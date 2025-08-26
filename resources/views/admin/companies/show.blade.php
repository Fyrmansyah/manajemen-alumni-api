@extends('layouts.app')
@section('title','Detail Perusahaan')
@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><i class="fas fa-building me-2"></i>Detail Perusahaan</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.companies.edit',$company) }}" class="btn btn-warning"><i class="fas fa-edit me-1"></i>Edit</a>
            <a href="{{ route('admin.companies.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Kembali</a>
        </div>
    </div>
    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        @if($company->logo)
                            <img src="{{ asset('storage/company_logos/'.$company->logo) }}" class="rounded border" style="max-width:160px;max-height:160px;object-fit:cover;" alt="Logo">
                        @else
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto" style="width:140px;height:140px;font-size:48px;">
                                {{ strtoupper(substr($company->company_name,0,2)) }}
                            </div>
                        @endif
                    </div>
                    <h5 class="mb-1">{{ $company->company_name }}</h5>
                    <span class="badge bg-{{ $company->status=='active'?'success':($company->status=='pending'?'warning':'danger') }} text-uppercase">{{ $company->status }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white"><strong>Informasi Umum</strong></div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-3">Email</dt><dd class="col-sm-9">{{ $company->email }}</dd>
                        <dt class="col-sm-3">Telepon</dt><dd class="col-sm-9">{{ $company->phone ?: '-' }}</dd>
                        <dt class="col-sm-3">Website</dt><dd class="col-sm-9">@if($company->website)<a href="{{ $company->website }}" target="_blank">{{ $company->website }}</a>@else - @endif</dd>
                        <dt class="col-sm-3">Industri</dt><dd class="col-sm-9">{{ $company->industry ?: '-' }}</dd>
                        <dt class="col-sm-3">Alamat</dt><dd class="col-sm-9">{{ $company->address ?: '-' }}</dd>
                    </dl>
                </div>
            </div>
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white"><strong>Deskripsi</strong></div>
                <div class="card-body">
                    <p class="mb-0">{{ $company->description ?: 'Tidak ada deskripsi.' }}</p>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-header bg-white"><strong>Statistik</strong></div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <h5 class="mb-1">{{ $company->jobs()->count() }}</h5>
                            <small class="text-muted">Total Lowongan</small>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <h5 class="mb-1">{{ $company->jobs()->active()->count() ?? 0 }}</h5>
                            <small class="text-muted">Lowongan Aktif</small>
                        </div>
                        <div class="col-md-4">
                            <h5 class="mb-1">{{ $company->jobs()->withCount('applications')->get()->sum('applications_count') }}</h5>
                            <small class="text-muted">Total Lamaran</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
