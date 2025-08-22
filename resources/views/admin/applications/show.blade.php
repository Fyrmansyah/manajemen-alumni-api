@extends('layouts.app')

@section('title', 'Detail Lamaran - Admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-file-alt text-primary me-2"></i>
                        Detail Lamaran
                    </h1>
                    <p class="text-muted mb-0">Lihat dan kelola detail lamaran pekerjaan</p>
                </div>
                <a href="{{ route('admin.applications.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>
                    Kembali
                </a>
            </div>

            <div class="row g-3">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="mb-1">{{ $application->job->title }}</h5>
                                    <p class="text-muted mb-0">
                                        <i class="fas fa-building me-1"></i>
                                        {{ $application->job->company->company_name ?? $application->job->company->name ?? 'Perusahaan' }}
                                    </p>
                                </div>
                                <div>
                                    @php
                                        $badge = [
                                            'submitted' => 'info',
                                            'reviewed' => 'warning',
                                            'interview' => 'primary',
                                            'accepted' => 'success',
                                            'rejected' => 'danger',
                                        ][$application->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $badge }}">{{ ucfirst($application->status) }}</span>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Tanggal Apply</small>
                                    <p class="mb-0">{{ $application->applied_at?->format('d M Y H:i') ?? $application->created_at->format('d M Y H:i') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Tanggal Ditinjau</small>
                                    <p class="mb-0">{{ $application->reviewed_at?->format('d M Y H:i') ?? '-' }}</p>
                                </div>
                                @if($application->interview_at)
                                <div class="col-md-6 mt-3">
                                    <small class="text-muted d-block">Jadwal Interview</small>
                                    <p class="mb-0">{{ $application->interview_at->format('d M Y H:i') }}</p>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <small class="text-muted d-block">Lokasi/Media</small>
                                    <p class="mb-0">{{ $application->interview_location ?? '-' }}</p>
                                </div>
                                @endif
                            </div>

                            @if($application->cover_letter)
                                <hr>
                                <h6>Cover Letter</h6>
                                <div class="p-3 bg-light rounded">{!! nl2br(e($application->cover_letter)) !!}</div>
                            @endif

                            @if($application->notes)
                                <hr>
                                <h6>Catatan</h6>
                                <div class="p-3 bg-primary bg-opacity-10 rounded text-primary">{!! nl2br(e($application->notes)) !!}</div>
                            @endif

                            @if($application->cv_file)
                                <hr>
                                <a href="{{ asset('storage/cvs/'.$application->cv_file) }}" target="_blank" class="btn btn-outline-success">
                                    <i class="fas fa-file-pdf me-1"></i> Lihat CV
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0">
                            <h6 class="mb-0">Aksi</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <form action="{{ route('admin.applications.updateStatus', $application) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="reviewed">
                                    <button type="submit" class="btn btn-warning"><i class="fas fa-eye me-1"></i> Mark as Reviewed</button>
                                </form>

                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#interviewModal">
                                    <i class="fas fa-calendar me-1"></i> Schedule Interview
                                </button>

                                <form action="{{ route('admin.applications.updateStatus', $application) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="accepted">
                                    <button type="submit" class="btn btn-success"><i class="fas fa-check me-1"></i> Accept</button>
                                </form>

                                <form action="{{ route('admin.applications.updateStatus', $application) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="btn btn-danger"><i class="fas fa-times me-1"></i> Reject</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Interview Modal -->
<div class="modal fade" id="interviewModal" tabindex="-1" aria-labelledby="interviewModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="interviewModalLabel">Schedule Interview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('admin.applications.updateStatus', $application) }}" method="POST">
        @csrf
        @method('PATCH')
        <div class="modal-body">
            <input type="hidden" name="status" value="interview">
            <div class="mb-3">
                <label class="form-label">Tanggal & Waktu</label>
                <input type="datetime-local" name="interview_at" class="form-control" value="{{ $application->interview_at ? $application->interview_at->format('Y-m-d\\TH:i') : '' }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Lokasi/Media</label>
                <input type="text" name="interview_location" class="form-control" placeholder="Kantor, Zoom, Google Meet, dll" value="{{ $application->interview_location }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Detail</label>
                <textarea name="interview_details" class="form-control" rows="3" placeholder="Instruksi tambahan, PIC, link meeting, dsb">{{ $application->interview_details }}</textarea>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Jadwal</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
.badge { font-size: 0.8rem; }
</style>
@endpush
