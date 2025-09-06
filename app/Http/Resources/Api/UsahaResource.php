<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\KepemilikanUsahaResource;
use App\Http\Resources\RangeLabaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsahaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'alumni' => AlumniResource::make($this->whenLoaded('alumni')),
            'tgl_mulai' => $this->tgl_mulai,
            'tgl_selesai' => $this->tgl_selesai,
            'nama_perusahaan' => $this->nama_perusahaan,
            'bidang' => $this->bidang,
            'jml_karyawan' => $this->jml_karyawan,
            'sesuai_jurusan' => $this->sesuai_jurusan,
            'kepemilikan_usaha' => KepemilikanUsahaResource::make($this->whenLoaded('kepemilikan_usaha')),
            'range_laba' => RangeLabaResource::make($this->whenLoaded('range_laba')),
        ];
    }
}
