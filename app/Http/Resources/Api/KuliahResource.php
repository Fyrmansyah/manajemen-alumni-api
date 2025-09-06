<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KuliahResource extends JsonResource
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
            'nama_kampus' => $this->nama_kampus,
            'prodi' => $this->prodi,
            'tahun_masuk' => $this->tahun_masuk,
            'tahun_lulus' => $this->tahun_lulus,
            'sesuai_jurusan' => $this->sesuai_jurusan,
            'jalur_masuk' => AlumniResource::make($this->whenLoaded('jalur_masuk')),
        ];
    }
}
