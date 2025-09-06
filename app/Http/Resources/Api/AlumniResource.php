<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\JurusanResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlumniResource extends JsonResource
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
            'nama' => $this->nama,
            'jenis_kelamin' => $this->jenis_kelamin,
            'tgl_lahir' => $this->tgl_lahir,
            'tahun_mulai' => $this->tahun_mulai,
            'tahun_lulus' => $this->tahun_lulus,
            'no_tlp' => $this->no_tlp,
            'email' => $this->email,
            'password' => $this->password,
            'tempat_lahir' => $this->tempat_lahir,
            'alamat_jalan' => $this->alamat_jalan,
            'alamat_kode_pos' => $this->alamat_kode_pos,
            'alamat_kecamatan' => $this->alamat_kecamatan,
            'alamat_kelurahan' => $this->alamat_kelurahan,
            'alamat_desa' => $this->alamat_desa,
            'alamat_rw' => $this->alamat_rw,
            'alamat_rt' => $this->alamat_rt,
            'jurusan' => JurusanResource::make($this->whenLoaded('jurusan')),
            'nisn' => NisnResource::make($this->whenLoaded('nisn')),
            'kuliahs' => KuliahResource::collection($this->whenLoaded('kuliahs')),
            'kerjas' => KerjaResource::collection($this->whenLoaded('kerjas')),
            'usahas' => UsahaResource::collection($this->whenLoaded('usahas')),
        ];
    }
}
