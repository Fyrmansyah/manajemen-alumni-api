<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomepageSlide extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'caption',
        'image',
        'button_text',
        'button_link',
        'is_active',
        'sort_order',
    ];
}
