<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Company extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'user_id',
        'company_name',
        'email',
        'password',
        'phone',
        'address',
        'website',
        'description',
        'industry',
        'category_id',
        'established_year',
        'company_size',
        'contact_person',
        'contact_person_phone',
        'contact_position',
        'logo',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed',
        'is_approved' => 'boolean',
        'established_year' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Jurusan::class, 'category_id');
    }

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }

    public function applications()
    {
        return $this->hasManyThrough(Application::class, Job::class);
    }

    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset('storage/company_logos/' . $this->logo) : null;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'aktif')->where('is_approved', true);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
