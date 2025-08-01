<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jurusan extends Model
{
    protected $guarded = ['id'];

    /**
     * Get the alumni for this jurusan.
     */
    public function alumni(): HasMany
    {
        return $this->hasMany(Alumni::class, 'jurusan_id');
    }
}
