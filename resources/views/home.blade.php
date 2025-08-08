@extends('layouts.app')

@section('title', 'BKK SMKN 1 Surabaya - Portal Karir Alumni')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center min-vh-75">
            <div class="col-lg-6">
                <div class="hero-content">
                    <h1 class="hero-title">
                        Portal Karir Alumni<br>
                        <span class="text-warning">SMK Negeri 1 Surabaya</span>
                    </h1>
                    <p class="hero-subtitle">
                        Jembatan menuju karir impian Anda. Temukan lowongan kerja terbaik dari perusahaan-perusahaan terpercaya yang sesuai dengan keahlian Anda.
                    </p>
                    <div class="hero-buttons">
                        <a href="{{ route('jobs.index') }}" class="btn btn-warning btn-lg me-3">
                            <i class="fas fa-search me-2"></i>Cari Lowongan
                        </a>
                        @guest
                            <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Daftar Alumni
                            </a>
                        @endguest
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-image text-center">
                    <div class="logo-container">
                        <img src="{{ asset('assets/images/logo BKK.png') }}" alt="SMK Negeri 1 Surabaya" class="hero-logo">   
                    </div>
                </div>
            </div>
        </div>
    </div>
                                                                                                                                            
</section>

<!-- Statistics Section -->
<section class="stats-section">
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="stat-card text-center">
                    <div class="stat-icon text-primary">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <div class="stat-number">{{ $stats['total_jobs'] ?? 0 }}</div>
                    <div class="stat-label">Lowongan</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="stat-card text-center">
                    <div class="stat-icon text-success">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="stat-number">{{ $stats['total_companies'] ?? 0 }}</div>
                    <div class="stat-label">Perusahaan Partner</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="stat-card text-center">
                    <div class="stat-icon text-info">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="stat-number">{{ $stats['total_alumni'] ?? 0 }}</div>
                    <div class="stat-label">Alumni Terdaftar</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="stat-card text-center">
                    <div class="stat-icon text-warning">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <div class="stat-number">{{ $stats['total_applications'] ?? 0 }}</div>
                    <div class="stat-label">Lamaran Berhasil</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Latest Jobs Section -->
<section class="jobs-section py-5">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title">Lowongan Terbaru</h2>
            <p class="section-subtitle">Temukan peluang karir terbaik yang sesuai dengan keahlian dan minat Anda</p>
        </div>
        
        <div class="row">
            @forelse($latest_jobs as $job)
                <div class="col-lg-6 mb-4">
                    <div class="job-card">
                        <div class="job-card-header">
                            <div class="company-info">
                                <div class="company-logo">
                                    @if(!empty($job->company->logo))
                                        <img src="{{ asset('storage/company_logos/' . $job->company->logo) }}" 
                                             alt="{{ $job->company->company_name }}">
                                    @else
                                        <div class="company-initial">
                                            {{ strtoupper(substr($job->company->company_name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="company-details">
                                    <h5 class="job-title">{{ $job->title }}</h5>
                                    <p class="company-name">{{ $job->company->company_name }}</p>
                                </div>
                            </div>
                            <div class="job-type-badge">
                                <span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', $job->type)) }}</span>
                            </div>
                        </div>
                        
                        <div class="job-card-body">
                            <div class="job-meta">
                                <div class="job-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>{{ $job->location }}</span>
                                </div>
                                @if($job->salary_min && $job->salary_max)
                                    <div class="job-salary">
                                        <i class="fas fa-money-bill-wave"></i>
                                        <span>Rp {{ number_format($job->salary_min, 0, ',', '.') }} - Rp {{ number_format($job->salary_max, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                            </div>
                            <p class="job-description">{{ Str::limit($job->description, 100) }}</p>
                        </div>
                        
                        <div class="job-card-footer">
                            <span class="job-date">{{ $job->created_at->diffForHumans() }}</span>
                            <a href="{{ route('jobs.show', $job->id) }}" class="btn btn-outline-primary">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="empty-state">
                        <i class="fas fa-briefcase"></i>
                        <h4>Belum ada lowongan terbaru</h4>
                        <p>Lowongan akan segera diperbarui</p>
                    </div>
                </div>
            @endforelse
        </div>
        
        @if($latest_jobs->isNotEmpty())
            <div class="text-center mt-5">
                <a href="{{ route('jobs.index') }}" class="btn btn-primary btn-lg">
                    Lihat Semua Lowongan
                    <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        @endif
    </div>
</section>

<!-- Latest News Section -->
<section class="news-section py-5 bg-light">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title">Berita & Informasi</h2>
            <p class="section-subtitle">Dapatkan informasi terkini seputar dunia kerja dan karir</p>
        </div>
        
        <div class="row">
            @forelse($latest_news as $news)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="news-card">
                        <div class="news-content">
                            <h5 class="news-title">
                                <a href="{{ route('news.show', $news->id) }}">{{ $news->title }}</a>
                            </h5>
                            <p class="news-excerpt">{{ Str::limit($news->content, 120) }}</p>
                        </div>
                        <div class="news-footer">
                            <div class="news-meta">
                                <span class="news-author">
                                    <i class="fas fa-user"></i>
                                    Admin BKK
                                </span>
                                <span class="news-date">
                                    <i class="fas fa-calendar"></i>
                                    {{ $news->created_at->format('d M Y') }}
                                </span>
                            </div>
                            <a href="{{ route('news.show', $news->id) }}" class="btn btn-outline-primary btn-sm">
                                Baca Selengkapnya
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="empty-state">
                        <i class="fas fa-newspaper"></i>
                        <h4>Belum ada berita terbaru</h4>
                        <p>Berita akan segera diperbarui</p>
                    </div>
                </div>
            @endforelse
        </div>
        
        @if($latest_news->isNotEmpty())
            <div class="text-center mt-5">
                <a href="{{ route('news.index') }}" class="btn btn-primary btn-lg">
                    Lihat Semua Berita
                    <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        @endif
    </div>
</section>


@endsection

@section('styles')
<style>
/* Hero Section */
.hero-section {
    background: linear-gradient(135deg, #012340 0%, #012340 50%, #012340 100%);
    position: relative;
    min-height: 100vh;
    display: flex;
    align-items: center;
    color: white;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="1" fill="rgba(255,255,255,0.1)"/></svg>') repeat;
    background-size: 50px 50px;
    animation: float 20s infinite linear;
}

@keyframes float {
    0% { transform: translateY(0); }
    100% { transform: translateY(-100px); }
}

.min-vh-75 {
    min-height: 75vh;
}

.hero-content {
    z-index: 2;
    position: relative;
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 800;
    line-height: 1.2;
    margin-bottom: 1.5rem;
    animation: fadeInUp 1s ease-out;
}

.hero-subtitle {
    font-size: 1.3rem;
    line-height: 1.6;
    margin-bottom: 2rem;
    opacity: 0.9;
    animation: fadeInUp 1s ease-out 0.2s both;
}

.hero-buttons {
    animation: fadeInUp 1s ease-out 0.4s both;
}

.hero-image {
    z-index: 2;
    position: relative;
}

.logo-container {
    position: relative;
    display: inline-block;
    animation: fadeInScale 1s ease-out 0.6s both;
}

.hero-logo {
    width: 350px;
    height: 350px;
    object-fit: contain;
    filter: drop-shadow(0 10px 30px rgba(0,0,0,0.3));
}

.logo-text {
    position: absolute;
    right: -50px;
    bottom: 20px;
}

.school-name {
    font-size: 3rem;
    font-weight: 900;
    color: white;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.hero-wave {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    overflow: hidden;
    line-height: 0;
    transform: rotate(180deg);
}

.hero-wave svg {
    position: relative;
    display: block;
    width: calc(100% + 1.3px);
    height: 60px;
}

/* Statistics Section */
.stats-section {
    padding: 80px 0;
    background: white;
    position: relative;
    margin-top: -1px;
}

.stat-card {
    background: white;
    padding: 2rem;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    border-top: 4px solid transparent;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #3b82f6, #6366f1);
}

.stat-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 60px rgba(0,0,0,0.15);
}

.stat-card:nth-child(1)::before { background: linear-gradient(90deg, #3b82f6, #1d4ed8); }
.stat-card:nth-child(2)::before { background: linear-gradient(90deg, #10b981, #059669); }
.stat-card:nth-child(3)::before { background: linear-gradient(90deg, #06b6d4, #0891b2); }
.stat-card:nth-child(4)::before { background: linear-gradient(90deg, #f59e0b, #d97706); }

.stat-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.stat-number {
    font-size: 3rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
    color: #1e293b;
}

.stat-label {
    font-size: 1.1rem;
    color: #64748b;
    font-weight: 500;
}

/* Section Headers */
.section-header {
    margin-bottom: 4rem;
}

.section-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
    
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 60px;
    height: 4px;
    border-radius: 2px;
}

.section-subtitle {
    font-size: 1.2rem;
    color: #64748b;
    max-width: 600px;
    margin: 0 auto;
}

/* Jobs Section */
.jobs-section {
    padding: 80px 0;
    background: #f8fafc;
}

.job-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    overflow: hidden;
    height: 100%;
    border: 1px solid #e2e8f0;
}

.job-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 40px rgba(0,0,0,0.15);
    border-color: #3b82f6;
}

.job-card-header {
    padding: 1.5rem;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.company-info {
    display: flex;
    align-items: center;
    flex: 1;
}

.company-logo {
    width: 60px;
    height: 60px;
    margin-right: 1rem;
    flex-shrink: 0;
}

.company-logo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 12px;
}

.company-initial {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #3b82f6, #6366f1);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: 700;
    border-radius: 12px;
}

.company-details {
    flex: 1;
}

.job-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 0.25rem;
    line-height: 1.3;
}

.company-name {
    color: #64748b;
    margin: 0;
    font-size: 0.95rem;
}

.job-type-badge .badge {
    font-size: 0.75rem;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 500;
}

.job-card-body {
    padding: 1.5rem;
}

.job-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1rem;
}

.job-location,
.job-salary {
    display: flex;
    align-items: center;
    color: #64748b;
    font-size: 0.9rem;
}

.job-location i,
.job-salary i {
    margin-right: 0.5rem;
    color: #3b82f6;
}

.job-description {
    color: #64748b;
    line-height: 1.6;
    margin: 0;
}

.job-card-footer {
    padding: 1.5rem;
    border-top: 1px solid #f1f5f9;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.job-date {
    color: #94a3b8;
    font-size: 0.875rem;
}

/* News Section */
.news-section {
    padding: 80px 0;
}

.news-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    overflow: hidden;
    height: 100%;
    border: 1px solid #e2e8f0;
}

.news-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 40px rgba(0,0,0,0.15);
    border-color: #3b82f6;
}

.news-content {
    padding: 1.5rem;
    flex: 1;
}

.news-title {
    margin-bottom: 1rem;
}

.news-title a {
    color: #1e293b;
    text-decoration: none;
    font-weight: 600;
    font-size: 1.1rem;
    line-height: 1.4;
    transition: color 0.3s ease;
}

.news-title a:hover {
    color: #3b82f6;
}

.news-excerpt {
    color: #64748b;
    line-height: 1.6;
    margin: 0;
}

.news-footer {
    padding: 1.5rem;
    border-top: 1px solid #f1f5f9;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.news-meta {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.news-author,
.news-date {
    display: flex;
    align-items: center;
    color: #94a3b8;
    font-size: 0.875rem;
}

.news-author i,
.news-date i {
    margin-right: 0.5rem;
    width: 12px;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
}

.empty-state i {
    font-size: 4rem;
    color: #cbd5e1;
    margin-bottom: 1.5rem;
}

.empty-state h4 {
    color: #64748b;
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: #94a3b8;
    margin: 0;
}

/* Buttons */
.btn-warning {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    border: none;
    color: white;
    font-weight: 600;
    padding: 0.75rem 2rem;
    border-radius: 50px;
    transition: all 0.3s ease;
}

.btn-warning:hover {
    background: linear-gradient(135deg, #d97706, #b45309);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(245, 158, 11, 0.4);
    color: white;
}

.btn-outline-light {
    border: 2px solid rgba(255,255,255,0.3);
    color: white;
    font-weight: 600;
    padding: 0.75rem 2rem;
    border-radius: 50px;
    transition: all 0.3s ease;
}

.btn-outline-light:hover {
    background: white;
    color: #1e40af;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255, 255, 255, 0.3);
}

.btn-primary {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    border: none;
    font-weight: 600;
    padding: 0.75rem 2rem;
    border-radius: 50px;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #1d4ed8, #1e40af);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
}

.btn-outline-primary {
    border: 2px solid #3b82f6;
    color: #3b82f6;
    font-weight: 500;
    padding: 0.5rem 1.5rem;
    border-radius: 25px;
    transition: all 0.3s ease;
}

.btn-outline-primary:hover {
    background: #3b82f6;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInScale {
    from {
        opacity: 0;
        transform: scale(0.8);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-subtitle {
        font-size: 1.1rem;
    }
    
    .hero-logo {
        width: 150px;
        height: 150px;
    }
    
    .school-name {
        font-size: 2rem;
    }
    
    .section-title {
        font-size: 2rem;
    }
    
    .stat-card {
        padding: 1.5rem;
    }
    
    .stat-number {
        font-size: 2.5rem;
    }
    
    .job-card-header {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .news-footer {
        flex-direction: column;
        align-items: flex-start;
    }
}

@media (max-width: 576px) {
    .hero-buttons {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    
    .hero-buttons .btn {
        width: 100%;
    }
}
</style>
@endsection