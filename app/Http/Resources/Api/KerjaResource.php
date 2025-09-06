<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\DurasiKerjaResource;
use App\Http\Resources\JenisPerusahaanResource;
use App\Http\Resources\MasaTungguKerjaResource;
use App\Http\Resources\RangeGajiResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KerjaResource extends JsonResource
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
            'nama_perusahaan' => $this->nama_perusahaan,
            'alamat_perusahaan' => $this->alamat_perusahaan,
            'tgl_mulai' => $this->tgl_mulai,
            'tgl_selesai' => $this->tgl_selesai,
            'sesuai_jurusan' => $this->sesuai_jurusan,
            'jabatan' => $this->jabatan,
            'masa_tunggu_kerja' => MasaTungguKerjaResource::make($this->whenLoaded('masa_tunggu_kerja')),
            'jenis_perusahaan' => JenisPerusahaanResource::make($this->whenLoaded('jenis_perusahaan')),
            'durasi_kerja' => DurasiKerjaResource::make($this->whenLoaded('durasi_kerja')),
            'range_gaji' => RangeGajiResource::make($this->whenLoaded('range_gaji')),
        ];
    }
}
