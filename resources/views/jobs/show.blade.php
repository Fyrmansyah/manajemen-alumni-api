@extends('layouts.app')

@section('title', $job->title . ' - BKK SMKN 1 Surabaya')

@section('content')
<div class="container mt-4 mb-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('jobs.index') }}">Lowongan Kerja</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($job->title, 30) }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body p-4">
                    <!-- Job Header -->
                    <div class="row align-items-start mb-4">
                        <div class="col-auto">
                            @if(!empty($job->company->logo))
                                <img src="{{ asset('storage/company_logos/' . $job->company->logo) }}" 
                                     alt="{{ $job->company->company_name }}" 
                                     class="rounded" 
                                     style="width: 80px; height: 80px; object-fit: cover;">
                            @else
                                <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center" 
                                     style="width: 80px; height: 80px; font-size: 2rem;">
                                    {{ strtoupper(substr($job->company->company_name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="col">
                            <h1 class="h2 mb-2">{{ $job->title }}</h1>
                            <p class="text-muted mb-2">
                                <i class="fas fa-building me-2"></i>{{ $job->company->company_name }}
                            </p>
                            <div class="row text-muted">
                                <div class="col-md-6">
                                    <p class="mb-1">
                                        <i class="fas fa-map-marker-alt me-2"></i>{{ $job->location }}
                                    </p>
                                    <p class="mb-1">
                                        <i class="fas fa-clock me-2"></i>{{ ucfirst(str_replace('_', ' ', $job->type)) }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1">
                                        <i class="fas fa-users me-2"></i>{{ $job->positions_available }} posisi tersedia
                                    </p>
                                    <p class="mb-1">
                                        <i class="fas fa-calendar me-2"></i>{{ $job->created_at->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Job Details -->
                    <div class="mb-4">
                        <h4>Deskripsi Pekerjaan</h4>
                        <div class="job-description">
                            {!! nl2br(e($job->description)) !!}
                        </div>
                    </div>

                    @if($job->requirements)
                    <div class="mb-4">
                        <h4>Persyaratan</h4>
                        <div class="job-requirements">
                            {!! nl2br(e($job->requirements)) !!}
                        </div>
                    </div>
                    @endif

                    @if($job->benefits)
                    <div class="mb-4">
                        <h4>Keuntungan & Fasilitas</h4>
                        <div class="job-benefits">
                            {!! nl2br(e($job->benefits)) !!}
                        </div>
                    </div>
                    @endif

                    <!-- Company Info -->
                    <div class="border-top pt-4">
                        <h4>Tentang Perusahaan</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Nama Perusahaan:</strong> {{ $job->company->company_name }}</p>
                                <p><strong>Alamat:</strong> {{ $job->company->address }}</p>
                                @if($job->company->phone)
                                    <p><strong>Telepon:</strong> {{ $job->company->phone }}</p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                @if($job->company->email)
                                    <p><strong>Email:</strong> {{ $job->company->email }}</p>
                                @endif
                                @if($job->company->website)
                                    <p><strong>Website:</strong> 
                                        <a href="{{ $job->company->website }}" target="_blank" class="text-primary">
                                            {{ $job->company->website }}
                                        </a>
                                    </p>
                                @endif
                                @if($job->company->industry)
                                    <p><strong>Industri:</strong> {{ $job->company->industry }}</p>
                                @endif
                            </div>
                        </div>
                        @if($job->company->description)
                            <p class="mt-3">{{ $job->company->description }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Related Jobs -->
            @if($relatedJobs->count() > 0)
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Lowongan Terkait</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($relatedJobs as $relatedJob)
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3 h-100">
                                <h6>
                                    <a href="{{ route('jobs.show', $relatedJob->id) }}" class="text-decoration-none">
                                        {{ $relatedJob->title }}
                                    </a>
                                </h6>
                                <p class="text-muted small mb-1">{{ $relatedJob->company->company_name }}</p>
                                <p class="text-muted small mb-0">
                                    <i class="fas fa-map-marker-alt me-1"></i>{{ $relatedJob->location }}
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Apply Section -->
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    @if($job->salary_min && $job->salary_max)
                        <h4 class="text-success mb-3">
                            Rp {{ number_format($job->salary_min, 0, ',', '.') }} - 
                            Rp {{ number_format($job->salary_max, 0, ',', '.') }}
                        </h4>
                    @endif

                    @auth('alumni')
                        @if($hasApplied)
                            @php
                                $application = auth('alumni')->user()->applications()->where('job_posting_id', $job->id)->first();
                                $statusText = match($application->status) {
                                    'submitted' => 'Menunggu Review',
                                    'reviewed' => 'Sedang Direview',
                                    'interview' => 'Dijadwalkan Interview',
                                    'accepted' => 'Diterima',
                                    'rejected' => 'Ditolak',
                                    default => 'Diproses'
                                };
                                $statusColor = match($application->status) {
                                    'submitted' => 'info',
                                    'reviewed' => 'warning',
                                    'interview' => 'primary',
                                    'accepted' => 'success',
                                    'rejected' => 'danger',
                                    default => 'secondary'
                                };
                            @endphp
                            <div class="alert alert-{{ $statusColor }}">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Status Lamaran Anda:</strong> {{ $statusText }}
                                @if($application->applied_at)
                                    <br><small class="text-muted">Dilamar pada: {{ $application->applied_at->format('d M Y, H:i') }}</small>
                                @endif
                            </div>
                            <a href="{{ route('alumni.applications') }}" class="btn btn-outline-primary">
                                <i class="fas fa-eye me-2"></i>Lihat Detail Lamaran
                            </a>
                        @elseif(auth('alumni')->user()->profile_completion < 80)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Profil Belum Lengkap</strong><br>
                                Silakan lengkapi profil Anda terlebih dahulu sebelum melamar pekerjaan
                            </div>
                            <a href="{{ route('alumni.profile') }}" class="btn btn-warning btn-lg w-100 mb-3">
                                <i class="fas fa-user-edit me-2"></i>Lengkapi Profil
                            </a>
                        @elseif(!$job->canApply())
                            <div class="alert alert-danger">
                                <i class="fas fa-times-circle me-2"></i>
                                <strong>Lowongan Tidak Tersedia</strong><br>
                                @if($job->status !== 'active')
                                    Lowongan ini sudah tidak aktif
                                @elseif($job->isExpired())
                                    Lowongan ini sudah melewati batas waktu pendaftaran ({{ $job->application_deadline->format('d M Y') }})
                                @elseif($job->positions_available && $job->active_application_count >= $job->positions_available)
                                    Lowongan ini sudah penuh ({{ $job->positions_available }} posisi tersedia)
                                @else
                                    Lowongan ini sudah tidak tersedia
                                @endif
                            </div>
                        @else
                            <button class="btn btn-primary btn-lg w-100 mb-3" onclick="applyJob({{ $job->id }})">
                                <i class="fas fa-paper-plane me-2"></i>Lamar Sekarang
                            </button>
                        @endif
                    @elseauth('admin')
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle me-2"></i>
                            Admin tidak dapat melamar pekerjaan
                        </div>
                    @elseauth('company')
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle me-2"></i>
                            Perusahaan tidak dapat melamar pekerjaan
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg w-100 mb-3">
                            <i class="fas fa-sign-in-alt me-2"></i>Login untuk Melamar
                        </a>
                        <p class="small text-muted">
                            Belum punya akun? 
                            <a href="{{ route('register') }}">Daftar di sini</a>
                        </p>
                    @endauth

                    <div class="d-grid gap-2 mt-3">
                        <button class="btn btn-outline-secondary" onclick="shareJob()">
                            <i class="fas fa-share-alt me-2"></i>Bagikan
                        </button>
                        <button class="btn btn-outline-secondary" onclick="saveJob({{ $job->id }})">
                            <i class="fas fa-bookmark me-2"></i>Simpan
                        </button>
                    </div>
                </div>
            </div>

            <!-- Job Info -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Informasi Lowongan</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-5"><strong>Status:</strong></div>
                        <div class="col-7">
                            <span class="badge bg-{{ $job->status === 'active' ? 'success' : 'secondary' }}">
                                {{ ucfirst($job->status) }}
                            </span>
                        </div>
                    </div>
                    @if($job->application_deadline)
                    <div class="row mb-2">
                        <div class="col-5"><strong>Deadline:</strong></div>
                        <div class="col-7">{{ $job->application_deadline->format('d M Y') }}</div>
                    </div>
                    @endif
                    <div class="row mb-2">
                        <div class="col-5"><strong>Diposting:</strong></div>
                        <div class="col-7">{{ $job->created_at->format('d M Y') }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5"><strong>Total Pelamar:</strong></div>
                        <div class="col-7">{{ $job->application_count }} orang</div>
                    </div>
                    @if($job->positions_available)
                    <div class="row mb-2">
                        <div class="col-5"><strong>Posisi Tersedia:</strong></div>
                        <div class="col-7">
                            @if($job->available_positions > 0)
                                <span class="text-success">{{ $job->available_positions }} dari {{ $job->positions_available }}</span>
                            @else
                                <span class="text-danger">Penuh</span>
                            @endif
                        </div>
                    </div>
                    @endif
                    <div class="row">
                        <div class="col-5"><strong>ID Lowongan:</strong></div>
                        <div class="col-7">#{{ $job->id }}</div>
                    </div>
                </div>
            </div>

            <!-- Tips -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-lightbulb me-2"></i>Tips Melamar
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Baca deskripsi pekerjaan dengan teliti
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Pastikan kualifikasi Anda sesuai
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Siapkan CV dan portfolio terbaru
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success me-2"></i>
                            Tulis surat lamaran yang menarik
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Apply Job Modal -->
<div class="modal fade" id="applyModal" tabindex="-1" aria-labelledby="applyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="applyModalLabel">
                    <i class="fas fa-paper-plane me-2"></i>Lamar Pekerjaan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="applyModalBody">
                <form id="applyForm" onsubmit="event.preventDefault(); submitApplication();">
                    <div class="mb-3">
                        <label for="cover_letter" class="form-label">Cover Letter <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="cover_letter" name="cover_letter" rows="6" 
                                  placeholder="Tulis surat lamaran Anda di sini..." required 
                                  maxlength="2000" oninput="updateCharCount(this)"></textarea>
                        <div class="form-text">
                            <span id="charCount">0</span>/2000 karakter. Minimal 50 karakter. Jelaskan mengapa Anda cocok untuk posisi ini.
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="cv_file" class="form-label">Upload CV (Opsional)</label>
                        <input type="file" class="form-control" id="cv_file" name="cv_file" 
                               accept=".pdf,.doc,.docx">
                        <div class="form-text">Format: PDF, DOC, DOCX. Maksimal 2MB.</div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Tips:</strong> 
                        <ul class="mb-0 mt-2">
                            <li>Jelaskan pengalaman dan motivasi yang relevan</li>
                            <li>Sebutkan keahlian yang sesuai dengan posisi</li>
                            <li>Berikan contoh pencapaian yang relevan</li>
                            <li>Jelaskan mengapa Anda tertarik dengan perusahaan ini</li>
                        </ul>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Batal
                </button>
                <button type="button" class="btn btn-primary" id="submitApplicationBtn" onclick="submitApplication()">
                    <i class="fas fa-paper-plane me-2"></i>Kirim Lamaran
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Apply for job function
function applyJob(jobId) {
    // Check if modal exists
    const modal = document.getElementById('applyModal');
    if (!modal) {
        console.error('Modal not found');
        alert('Terjadi kesalahan: Modal tidak ditemukan');
        return;
    }
    
    // Show apply modal
    const applyModal = new bootstrap.Modal(modal);
    applyModal.show();
    
    // Set job ID for form submission
    const form = document.getElementById('applyForm');
    if (form) {
        form.action = `/jobs/${jobId}/apply`;
    } else {
        console.error('Form not found');
    }
}

// Submit application form
function submitApplication() {
    // Validate form first
    if (!validateApplicationForm()) {
        return;
    }
    
    const form = document.getElementById('applyForm');
    if (!form) {
        console.error('Form not found');
        alert('Terjadi kesalahan: Form tidak ditemukan');
        return;
    }
    
    const formData = new FormData(form);
    const submitBtn = document.getElementById('submitApplicationBtn');
    const originalText = submitBtn.innerHTML;
    
    // Disable submit button and show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengirim...';
    
    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(async response => {
        const contentType = response.headers.get('content-type') || '';
        const isJson = contentType.includes('application/json');
        const data = isJson ? await response.json() : null;

        // Handle both success and error responses consistently
        if (response.ok && data && data.success) {
            // Show success message
            document.getElementById('applyModalBody').innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                    <h5 class="text-success">Lamaran Berhasil Dikirim!</h5>
                    <p class="text-muted">Terima kasih telah melamar. Tim kami akan segera meninjau lamaran Anda.</p>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="location.reload()">
                        Tutup
                    </button>
                </div>
            `;
        } else {
            let msg = (data && data.message) ? data.message : '';
            // Extract first validation error if present
            if (!msg && data && data.errors) {
                try {
                    const firstField = Object.keys(data.errors)[0];
                    if (firstField && Array.isArray(data.errors[firstField]) && data.errors[firstField].length) {
                        msg = data.errors[firstField][0];
                    }
                } catch (_) { /* ignore */ }
            }
            // Detect session/login issues and non-JSON redirects
            if (!msg) {
                if (response.status === 419) {
                    msg = 'Sesi Anda telah berakhir. Muat ulang halaman lalu coba lagi.';
                } else if (response.status === 401 || response.redirected || (response.url && response.url.includes('/login'))) {
                    msg = 'Silakan login sebagai alumni untuk melamar.';
                } else {
                    msg = 'Terjadi kesalahan saat mengirim lamaran';
                }
            }
            // Show error message in modal
            document.getElementById('applyModalBody').innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-exclamation-triangle text-danger fa-3x mb-3"></i>
                    <h5 class="text-danger">Gagal Mengirim Lamaran</h5>
                    <p class="text-muted">${msg}</p>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Tutup
                    </button>
                </div>
            `;
            // Re-enable button for retry
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Fetch Error:', error);
        console.error('Error details:', {
            name: error.name,
            message: error.message,
            stack: error.stack
        });
        
        // Show error message in modal instead of alert
        document.getElementById('applyModalBody').innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-exclamation-triangle text-danger fa-3x mb-3"></i>
                <h5 class="text-danger">Terjadi Kesalahan</h5>
                <p class="text-muted">Tidak dapat mengirim lamaran. Silakan periksa koneksi internet Anda dan coba lagi.</p>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="location.reload()">
                    Coba Lagi
                </button>
            </div>
        `;
        
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

// Update character count
function updateCharCount(textarea) {
    const charCount = document.getElementById('charCount');
    const count = textarea.value.length;
    charCount.textContent = count;
    
    if (count < 50) {
        charCount.className = 'text-danger';
    } else if (count > 1800) {
        charCount.className = 'text-warning';
    } else if (count > 1900) {
        charCount.className = 'text-danger';
    } else {
        charCount.className = 'text-success';
    }
}

// Validate form before submission
function validateApplicationForm() {
    const coverLetter = document.getElementById('cover_letter').value.trim();
    const cvFile = document.getElementById('cv_file').files[0];
    
    // Check cover letter length
    if (coverLetter.length < 50) {
        alert('Cover letter minimal 50 karakter');
        return false;
    }
    
    if (coverLetter.length > 2000) {
        alert('Cover letter maksimal 2000 karakter');
        return false;
    }
    
    // Check CV file size if uploaded
    if (cvFile && cvFile.size > 2 * 1024 * 1024) { // 2MB
        alert('File CV maksimal 2MB');
        return false;
    }
    
    return true;
}

// Reset form when modal is hidden
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('applyModal');
    if (modal) {
        modal.addEventListener('hidden.bs.modal', function () {
            const form = document.getElementById('applyForm');
            if (form) {
                form.reset();
                const submitBtn = document.getElementById('submitApplicationBtn');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Kirim Lamaran';
                }
                
                // Reset modal body to original content
                const modalBody = document.getElementById('applyModalBody');
                if (modalBody) {
                    modalBody.innerHTML = `
                        <form id="applyForm" onsubmit="event.preventDefault(); submitApplication();">
                            <div class="mb-3">
                                <label for="cover_letter" class="form-label">Cover Letter <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="cover_letter" name="cover_letter" rows="6" 
                                          placeholder="Tulis surat lamaran Anda di sini..." required 
                                          maxlength="2000" oninput="updateCharCount(this)"></textarea>
                                <div class="form-text">
                                    <span id="charCount">0</span>/2000 karakter. Minimal 50 karakter. Jelaskan mengapa Anda cocok untuk posisi ini.
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="cv_file" class="form-label">Upload CV (Opsional)</label>
                                <input type="file" class="form-control" id="cv_file" name="cv_file" 
                                       accept=".pdf,.doc,.docx">
                                <div class="form-text">Format: PDF, DOC, DOCX. Maksimal 2MB.</div>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Tips:</strong> 
                                <ul class="mb-0 mt-2">
                                    <li>Jelaskan pengalaman dan motivasi yang relevan</li>
                                    <li>Sebutkan keahlian yang sesuai dengan posisi</li>
                                    <li>Berikan contoh pencapaian yang relevan</li>
                                    <li>Jelaskan mengapa Anda tertarik dengan perusahaan ini</li>
                                </ul>
                            </div>
                        </form>
                    `;
                    
                    // Reset character count
                    const charCount = document.getElementById('charCount');
                    if (charCount) {
                        charCount.textContent = '0';
                        charCount.className = '';
                    }
                }
            }
        });
    }
});

// Share job function
function shareJob() {
    if (navigator.share) {
        navigator.share({
            title: '{{ $job->title }}',
            text: 'Lowongan kerja di {{ $job->company->company_name }}',
            url: window.location.href
        });
    } else {
        // Fallback to copying URL to clipboard
        navigator.clipboard.writeText(window.location.href).then(function() {
            alert('Link berhasil disalin ke clipboard!');
        });
    }
}

// Save job function
@auth
function saveJob(jobId) {
    fetch(`/api/jobs/${jobId}/save`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Authorization': 'Bearer ' + localStorage.getItem('auth_token')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Lowongan berhasil disimpan!');
        } else {
            alert(data.message || 'Terjadi kesalahan saat menyimpan lowongan');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyimpan lowongan');
    });
}
@endauth
</script>
@endpush

@section('styles')
<style>
.job-description,
.job-requirements,
.job-benefits {
    line-height: 1.6;
}

.job-description ul,
.job-requirements ul,
.job-benefits ul {
    padding-left: 1.5rem;
}

.card-header h6 {
    color: #495057;
}
</style>
@endsection
