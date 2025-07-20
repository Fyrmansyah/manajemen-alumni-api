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
    ];

    protected $casts = [
        'salary_min' => 'float',
        'salary_max' => 'float',
        'application_deadline' => 'datetime',
        'positions_available' => 'integer',
    ];

    const JOB_TYPES = [
        'Full Time' => 'Full Time',
        'Part Time' => 'Part Time',
        'Kontrak' => 'Kontrak',
        'Freelance' => 'Freelance',
        'Magang' => 'Magang',
    ];

    const STATUSES = [
        'draft' => 'Draft',
        'active' => 'Active',
        'closed' => 'Closed',
        'expired' => 'Expired',
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
        return $query->where('status', 'active');
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

    public function canApply()
    {
        return $this->status === 'active' && !$this->isExpired();
    }
}
