<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Usaha extends Model
{
    protected $guarded = ['id'];

    public function alumni(): BelongsTo
    {
        return $this->belongsTo(Alumni::class);
    }

    public function kepemilikan_usaha(): BelongsTo
    {
        return $this->belongsTo(KepemilikanUsaha::class);
    }
    public function range_laba(): BelongsTo
    {
        return $this->belongsTo(RangeLaba::class);
    }
}
