<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RangeGaji extends Model
{
    protected $guarded = ['id'];

    public function kerjas(): HasMany
    {
        return $this->hasMany(Kerja::class);
    }
}
