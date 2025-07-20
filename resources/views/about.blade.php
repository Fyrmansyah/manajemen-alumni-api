@extends('layouts.app')

@section('title', 'Tentang BKK SMKN 1 Surabaya')

@section('content')
<div class="container mt-4 mb-5">
    <!-- Hero Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="bg-primary text-white p-5 rounded">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h1 class="display-4 fw-bold mb-3">Tentang BKK SMKN 1 Surabaya</h1>
                        <p class="lead mb-0">
                            Bursa Kerja Khusus yang berkomitmen menghubungkan alumni dengan peluang karir terbaik
                        </p>
                    </div>
                    <div class="col-lg-4 text-center">
                        <img src="https://www.smkn1-sby.sch.id/assets/template/landing/images/logo.png" 
                             alt="SMK Negeri 1 Surabaya" 
                             class="img-fluid"
                             style="max-height: 150px;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- About BKK -->
    <div class="row mb-5">
        <div class="col-lg-8 offset-lg-2">
            <div class="text-center mb-5">
                <h2 class="h1 fw-bold text-primary">Apa itu BKK?</h2>
                <p class="lead text-muted">
                    Bursa Kerja Khusus (BKK) adalah lembaga yang dibentuk di Sekolah Menengah Kejuruan 
                    untuk memberikan pelayanan dan informasi lowongan kerja kepada alumni.
                </p>
            </div>
        </div>
    </div>

    <!-- Vision & Mission -->
    <div class="row mb-5">
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                             style="width: 80px; height: 80px;">
                            <i class="fas fa-eye fa-2x"></i>
                        </div>
                    </div>
                    <h3 class="text-center text-primary mb-3">Visi</h3>
                    <p class="text-center text-muted">
                        Menjadi pusat informasi dan penempatan kerja yang terpercaya bagi alumni SMK Negeri 1 Surabaya 
                        untuk dapat bersaing di dunia kerja yang professional.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" 
                             style="width: 80px; height: 80px;">
                            <i class="fas fa-bullseye fa-2x"></i>
                        </div>
                    </div>
                    <h3 class="text-center text-success mb-3">Misi</h3>
                    <ul class="list-unstyled text-muted">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Menyalurkan alumni ke dunia kerja</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Memberikan informasi lowongan kerja</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Mengadakan pelatihan kerja</li>
                        <li class="mb-0"><i class="fas fa-check text-success me-2"></i>Menjalin kerjasama dengan industri</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Services -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="text-center mb-5">
                <h2 class="h1 fw-bold text-primary">Layanan Kami</h2>
                <p class="lead text-muted">Berbagai layanan yang kami sediakan untuk mendukung karir alumni</p>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body p-4">
                    <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 60px; height: 60px;">
                        <i class="fas fa-search fa-lg"></i>
                    </div>
                    <h5 class="text-primary mb-3">Pencarian Kerja</h5>
                    <p class="text-muted">
                        Platform digital untuk mencari dan melamar pekerjaan yang sesuai dengan keahlian dan minat.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body p-4">
                    <div class="bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 60px; height: 60px;">
                        <i class="fas fa-users fa-lg"></i>
                    </div>
                    <h5 class="text-primary mb-3">Konsultasi Karir</h5>
                    <p class="text-muted">
                        Bimbingan dan konsultasi untuk membantu alumni dalam mengembangkan karir profesional.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body p-4">
                    <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 60px; height: 60px;">
                        <i class="fas fa-graduation-cap fa-lg"></i>
                    </div>
                    <h5 class="text-primary mb-3">Pelatihan & Workshop</h5>
                    <p class="text-muted">
                        Program pelatihan dan workshop untuk meningkatkan skill dan daya saing di dunia kerja.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact -->
    <div class="row" id="contact">
        <div class="col-12">
            <div class="bg-light p-5 rounded">
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <h3 class="text-primary mb-4">Hubungi Kami</h3>
                        <div class="mb-3">
                            <i class="fas fa-map-marker-alt text-primary me-2"></i>
                            <strong>Alamat:</strong><br>
                            Jl. Smea No. 4, Wonokromo, Surabaya, Jawa Timur 60243
                        </div>
                        <div class="mb-3">
                            <i class="fas fa-phone text-primary me-2"></i>
                            <strong>Telepon:</strong> (031) 8292107
                        </div>
                        <div class="mb-3">
                            <i class="fas fa-envelope text-primary me-2"></i>
                            <strong>Email:</strong> info@smkn1sby.sch.id
                        </div>
                        <div class="mb-3">
                            <i class="fas fa-globe text-primary me-2"></i>
                            <strong>Website:</strong> 
                            <a href="https://www.smkn1-sby.sch.id" target="_blank" class="text-primary">
                                www.smkn1-sby.sch.id
                            </a>
                        </div>
                    </div>
                    
                    <div class="col-lg-6">
                        <h3 class="text-primary mb-4">Jam Operasional</h3>
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-2"><strong>Senin - Jumat</strong></div>
                                <div class="text-muted">07:00 - 15:00 WIB</div>
                            </div>
                            <div class="col-6">
                                <div class="mb-2"><strong>Sabtu</strong></div>
                                <div class="text-muted">07:00 - 12:00 WIB</div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <h5 class="text-primary mb-3">Ikuti Kami</h5>
                            <div class="d-flex gap-2">
                                <a href="#" class="btn btn-outline-primary">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="#" class="btn btn-outline-info">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="btn btn-outline-danger">
                                    <i class="fab fa-instagram"></i>
                                </a>
                                <a href="#" class="btn btn-outline-primary">
                                    <i class="fab fa-linkedin"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
