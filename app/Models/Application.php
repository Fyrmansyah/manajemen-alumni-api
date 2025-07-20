<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'alumni_id',
        'job_posting_id',
        'cover_letter',
        'cv_file',
        'status',
        'notes',
        'applied_at',
        'reviewed_at',
    ];

    protected $casts = [
        'applied_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    const STATUSES = [
        'submitted' => 'Submitted',
        'reviewed' => 'Reviewed',
        'interview' => 'Interview',
        'accepted' => 'Accepted',
        'rejected' => 'Rejected',
    ];

    public function alumni()
    {
        return $this->belongsTo(Alumni::class);
    }

    public function job()
    {
        return $this->belongsTo(Job::class, 'job_posting_id');
    }

    public function getCvFileUrlAttribute()
    {
        return $this->cv_file ? asset('storage/cvs/' . $this->cv_file) : null;
    }

    public function getStatusBadgeClassAttribute()
    {
        $classes = [
            'submitted' => 'bg-primary',
            'reviewed' => 'bg-info',
            'interview' => 'bg-warning',
            'accepted' => 'bg-success',
            'rejected' => 'bg-danger',
        ];

        return $classes[$this->status] ?? 'bg-secondary';
    }

    public function scopeForAlumni($query, $alumniId)
    {
        return $query->where('alumni_id', $alumniId);
    }

    public function scopeForJob($query, $jobId)
    {
        return $query->where('job_posting_id', $jobId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'submitted');
    }

    public function markAsReviewed()
    {
        $this->update([
            'status' => 'reviewed',
            'reviewed_at' => now(),
        ]);
    }

    public function accept($notes = null)
    {
        $this->update([
            'status' => 'accepted',
            'notes' => $notes,
            'reviewed_at' => now(),
        ]);
    }

    public function reject($notes = null)
    {
        $this->update([
            'status' => 'rejected',
            'notes' => $notes,
            'reviewed_at' => now(),
        ]);
    }
}
