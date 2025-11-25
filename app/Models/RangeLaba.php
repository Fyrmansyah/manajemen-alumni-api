<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RangeLaba extends Model
{
    protected $guarded = ['id'];

    public function usahas(): HasMany
    {
        return $this->hasMany(Usaha::class);
    }
}
