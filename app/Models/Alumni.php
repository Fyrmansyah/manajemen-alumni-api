<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Alumni extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    // Allow mass assignment for all columns except id so profile updates & foto work
    protected $guarded = ['id'];
    protected $hidden = ['password'];

    public function username(): string
    {
        // In case guard asks for identifier field name
        return 'nisn_id';
    }


    // -------------------------------------
    //          MODEL METHODS
    // -------------------------------------


    // -------------------------------------
    //          OVERRIDE METHODS
    // -------------------------------------
    protected function casts(): array
    {
        // Tambahkan cast is_verified agar selalu boolean di JSON
        return [
            'password' => 'hashed',
            'is_verified' => 'boolean',
        ];
    }


    // -------------------------------------
    //          RELATION METHODS
    // -------------------------------------
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function cvs()
    {
        return $this->hasMany(CV::class);
    }

    public function nisnNumber(): BelongsTo
    {
        return $this->belongsTo(Nisn::class, 'nisn_id');
    }

    public function getNisnAttribute(): ?string
    {
        // Virtual attribute to keep existing code working
        return $this->nisnNumber?->number;
    }

    public function appliedJobs()
    {
        return $this->belongsToMany(Job::class, 'applications', 'alumni_id', 'job_posting_id')
            ->withPivot(['status', 'cover_letter', 'cv_file', 'applied_at', 'reviewed_at'])
            ->withTimestamps();
    }

    public function hasAppliedFor(Job $job)
    {
        return $this->applications()->where('job_posting_id', $job->id)->exists();
    }

    public function getProfileCompletionAttribute()
    {
        $fields = [
            'nama_lengkap',
            'email',
            'phone',
            'alamat',
            'tanggal_lahir',
            'jenis_kelamin',
            'nisn',
            'tahun_lulus',
            'jurusan_id'
        ];

        $completedFields = 0;
        foreach ($fields as $field) {
            if (!empty($this->$field)) {
                $completedFields++;
            }
        }

        return round(($completedFields / count($fields)) * 100);
    }

    public function getNamaLengkapAttribute($value)
    {
        return $value ?: $this->nama;
    }

    /**
     * Build a public URL for the stored profile photo (foto) if available.
     * Handles cases where DB stores just the filename or already a relative path alumni_photos/filename.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (empty($this->foto)) {
            return null;
        }

        $relative = str_starts_with($this->foto, 'alumni_photos/')
            ? $this->foto
            : ('alumni_photos/' . ltrim($this->foto, '/'));

        return asset('storage/' . $relative);
    }

    public function getPhoneAttribute($value)
    {
        // Perbaiki fallback ke kolom no_hp (sebelumnya no_tlp menyebabkan selalu null)
        return $value ?: $this->no_hp;
    }

    public function getTanggalLahirAttribute($value)
    {
        return $value ?: $this->tgl_lahir;
    }

    public function savedJobs()
    {
        return $this->belongsToMany(Job::class, 'saved_jobs', 'alumni_id', 'job_posting_id')
            ->withTimestamps();
    }
}
