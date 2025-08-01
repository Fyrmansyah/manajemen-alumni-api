<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CV extends Model
{
    use HasFactory;

    protected $fillable = [
        'alumni_id',
        'title',
        'template',
        'filename',
        'data',
        'is_default',
    ];

    protected $casts = [
        'data' => 'array',
        'is_default' => 'boolean',
    ];

    public function alumni()
    {
        return $this->belongsTo(Alumni::class);
    }

    public function getDownloadUrlAttribute()
    {
        return asset('storage/cvs/' . $this->filename);
    }

    public function getPreviewUrlAttribute()
    {
        return route('alumni.cv.show', $this->id);
    }
}
