<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'user_id',
        'icon',
        'color',
        'read_at'
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime'
    ];

    // Relationships
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    // Methods
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    // Static methods for creating notifications
    public static function createCompanyRegistration($company)
    {
        $actionUrl = '#'; // Default fallback
        
        try {
            $actionUrl = route('admin.companies.show', $company->id);
        } catch (\Exception $e) {
            // Fallback to companies index if show route doesn't exist
            try {
                $actionUrl = route('admin.companies.index');
            } catch (\Exception $e2) {
                // Ultimate fallback
                try {
                    $actionUrl = route('admin.dashboard');
                } catch (\Exception $e3) {
                    $actionUrl = '#';
                }
            }
        }

        // Create one notification per admin so each admin can have individual read status
        $admins = Admin::all();
        $notifications = [];

        foreach ($admins as $admin) {
            $notifications[] = self::create([
                'type' => 'company_registered',
                'title' => 'Perusahaan Baru Terdaftar',
                'message' => "Perusahaan {$company->company_name} telah mendaftar dan menunggu verifikasi.",
                'data' => [
                    'company_id' => $company->id,
                    'company_name' => $company->company_name,
                    'company_email' => $company->email,
                    'action_url' => $actionUrl
                ],
                'icon' => 'fas fa-building',
                'color' => 'warning',
                'user_id' => $admin->id
            ]);
        }

        return $notifications;
    }

    public static function createJobApplication($application)
    {
        return self::create([
            'type' => 'job_application',
            'title' => 'Lamaran Kerja Baru',
            'message' => "Ada lamaran baru untuk posisi {$application->job->title}.",
            'data' => [
                'application_id' => $application->id,
                'job_title' => $application->job->title,
                'applicant_name' => $application->user->name,
                'action_url' => route('admin.applications.show', $application->id)
            ],
            'icon' => 'fas fa-user-tie',
            'color' => 'info'
        ]);
    }
}
