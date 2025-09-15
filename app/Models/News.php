<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'excerpt',
        'featured_image',
        'image_caption',
        'slug',
        'status',
        'is_featured',
        'category',
        'author_id',
        'published_at',
        'views',
        'tags',
        'meta_description',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'views' => 'integer',
        'is_featured' => 'boolean',
    ];

    public function author()
    {
        return $this->belongsTo(Admin::class, 'author_id');
    }

    public function getFeaturedImageUrlAttribute()
    {
        return $this->featured_image ? asset('storage/news/' . $this->featured_image) : null;
    }

    public function getExcerptAttribute($length = 150)
    {
        return strlen($this->content) > $length 
            ? substr(strip_tags($this->content), 0, $length) . '...'
            : strip_tags($this->content);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function incrementViews()
    {
        $this->increment('views');
    }
}
