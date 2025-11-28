<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kerja extends Model
{
    protected $guarded = ['id'];

    public function alumni(): BelongsTo
    {
        return $this->belongsTo(Alumni::class);
    }

    public function durasi_kerja(): BelongsTo
    {
        return $this->belongsTo(DurasiKerja::class);
    }

    public function jenis_perusahaan(): BelongsTo
    {
        return $this->belongsTo(JenisPerusahaan::class);
    }

    public function masa_tunggu_kerja(): BelongsTo
    {
        return $this->belongsTo(MasaTungguKerja::class);
    }

    public function range_gaji(): BelongsTo
    {
        return $this->belongsTo(RangeGaji::class);
    }
}
