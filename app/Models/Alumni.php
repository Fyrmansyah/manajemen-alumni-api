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

    protected $guarded = ['id'];
    protected $hidden = ['password'];


    // -------------------------------------
    //          MODEL METHODS
    // -------------------------------------


    // -------------------------------------
    //          OVERRIDE METHODS
    // -------------------------------------
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
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
            'nama_lengkap', 'email', 'phone', 'alamat', 'tanggal_lahir',
            'jenis_kelamin', 'nisn', 'tahun_lulus', 'jurusan_id'
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

    public function getPhoneAttribute($value)
    {
        return $value ?: $this->no_tlp;
    }

    public function getTanggalLahirAttribute($value)
    {
        return $value ?: $this->tgl_lahir;
    }
}
