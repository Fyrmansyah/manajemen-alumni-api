<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Job extends Model
{
    use HasFactory;

    protected $table = 'job_postings';

    protected $fillable = [
        'company_id',
        'title',
        'description',
        'requirements',
        'location',
        'type',
        'salary_min',
        'salary_max',
        'application_deadline',
        'status',
        'positions_available',
        'archived_at',
        'archive_reason',
    ];

    protected $casts = [
        'salary_min' => 'float',
        'salary_max' => 'float',
        'application_deadline' => 'datetime',
        'positions_available' => 'integer',
        'archived_at' => 'datetime',
    ];

    const JOB_TYPES = [
        'full_time' => 'Full Time',
        'part_time' => 'Part Time',
        'contract' => 'Kontrak',
        'freelance' => 'Freelance',
        'internship' => 'Magang',
    ];

    const STATUSES = [
        'draft' => 'Draft',
        'active' => 'Active', 
        'closed' => 'Closed',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'job_posting_id');
    }

    public function getFormattedSalaryAttribute()
    {
        if ($this->salary_min && $this->salary_max) {
            return 'Rp ' . number_format($this->salary_min, 0, ',', '.') . ' - Rp ' . number_format($this->salary_max, 0, ',', '.');
        } elseif ($this->salary_min) {
            return 'Rp ' . number_format($this->salary_min, 0, ',', '.') . '+';
        }
        return 'Negotiable';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')->whereNull('archived_at');
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhereHas('company', function ($company) use ($search) {
                  $company->where('company_name', 'like', "%{$search}%");
              });
        });
    }

    public function scopeByLocation($query, $location)
    {
        return $query->where('location', 'like', "%{$location}%");
    }

    public function scopeByJobType($query, $jobType)
    {
        return $query->where('type', $jobType);
    }

    public function isExpired()
    {
        return $this->application_deadline && $this->application_deadline < now();
    }

    /**
     * Get formatted days left until deadline
     * @return array|null ['days' => int, 'text' => string, 'is_urgent' => bool, 'is_expired' => bool] or null if no deadline
     */
    public function getDeadlineInfo(): ?array
    {
        if (!$this->application_deadline) {
            return null;
        }

        $daysLeft = now()->diffInDays($this->application_deadline, false);
        $daysLeftFormatted = floor(abs($daysLeft));
        $isUrgent = $daysLeft <= 7 && $daysLeft >= 0;
        $isExpired = $daysLeft < 0;

        if ($isExpired) {
            return [
                'days' => $daysLeftFormatted,
                'text' => 'Expired',
                'is_urgent' => false,
                'is_expired' => true,
                'css_class' => 'text-danger'
            ];
        } elseif ($isUrgent) {
            return [
                'days' => $daysLeftFormatted,
                'text' => $daysLeftFormatted . ' hari lagi',
                'is_urgent' => true,
                'is_expired' => false,
                'css_class' => 'text-warning'
            ];
        } else {
            return [
                'days' => $daysLeftFormatted,
                'text' => 'Deadline: ' . $this->application_deadline->format('d M Y'),
                'is_urgent' => false,
                'is_expired' => false,
                'css_class' => 'text-muted'
            ];
        }
    }

    public function canApply()
    {
        // Check if job is active
        if ($this->status !== 'active') {
            return false;
        }
        
        // Check if job is expired
        if ($this->isExpired()) {
            return false;
        }
        
        // Check if positions are still available
        if ($this->positions_available) {
            $activeApplications = $this->applications()
                ->whereIn('status', ['submitted', 'reviewed', 'interview', 'accepted'])
                ->count();
            
            if ($activeApplications >= $this->positions_available) {
                return false;
            }
        }
        
        return true;
    }

    public function getAvailablePositionsAttribute()
    {
        if (!$this->positions_available) {
            return null; // Unlimited positions
        }
        
        $activeApplications = $this->applications()
            ->whereIn('status', ['submitted', 'reviewed', 'interview', 'accepted'])
            ->count();
        
        return max(0, $this->positions_available - $activeApplications);
    }

    public function getApplicationCountAttribute()
    {
        return $this->applications()->count();
    }

    public function getActiveApplicationCountAttribute()
    {
        return $this->applications()
            ->whereIn('status', ['submitted', 'reviewed', 'interview', 'accepted'])
            ->count();
    }

    // Alumni who saved this job
    public function savedBy()
    {
        return $this->belongsToMany(Alumni::class, 'saved_jobs', 'job_posting_id', 'alumni_id')
                    ->withTimestamps();
    }

    /**
     * Get real-time status including auto-expired jobs
     */
    public function getRealTimeStatusAttribute()
    {
        // If job is archived, return archived status
        if ($this->isArchived()) {
            return 'archived';
        }
        
        // If job is expired but not yet archived, return expired
        if ($this->isExpired() && $this->status === 'active') {
            return 'expired';
        }
        
        // Return actual status
        return $this->status;
    }

    /**
     * Get status display text
     */
    public function getStatusDisplayAttribute()
    {
        $status = $this->getRealTimeStatusAttribute();
        
        switch ($status) {
            case 'active':
                return 'Aktif';
            case 'draft':
                return 'Draft';
            case 'closed':
                return 'Ditutup';
            case 'expired':
                return 'Kadaluarsa';
            case 'archived':
                return 'Diarsipkan';
            default:
                return ucfirst($status);
        }
    }

    /**
     * Get status CSS class
     */
    public function getStatusCssClassAttribute()
    {
        $status = $this->getRealTimeStatusAttribute();
        
        switch ($status) {
            case 'active':
                return 'success';
            case 'draft':
                return 'warning';
            case 'closed':
                return 'secondary';
            case 'expired':
                return 'danger';
            case 'archived':
                return 'dark';
            default:
                return 'secondary';
        }
    }

    // Scopes for archive functionality
    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }

    public function scopeExpiredButNotArchived($query)
    {
        return $query->whereNull('archived_at')
            ->where('application_deadline', '<', now())
            ->where('status', 'active');
    }

    // Archive methods
    public function archive($reason = 'Deadline expired')
    {
        $this->update([
            'archived_at' => now(),
            'archive_reason' => $reason,
            'status' => 'closed'  // Use 'closed' instead of 'expired' as per enum
        ]);
    }

    public function unarchive()
    {
        $this->update([
            'archived_at' => null,
            'archive_reason' => null,
            'status' => 'active'
        ]);
    }

    public function isArchived()
    {
        return !is_null($this->archived_at);
    }

    public function canBeReactivated()
    {
        return $this->isArchived() && auth('admin')->check();
    }
}
