<?php

namespace App\Http\Requests\Api;

use Illuminate\Validation\Rule;

class CreateAlumniRequest extends ApiFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(
            $this->baseRules(),
            $this->kuliahRules(),
            $this->kerjaRules(),
            $this->usahaRules(),
        );
    }

    private function baseRules(): array
    {
        return [
            'nisn' => 'required',
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'tgl_lahir' => 'required|date',
            'email' =>  ['required', 'email', Rule::unique('alumnis', 'email')->ignore($this->route('alumni_id'))],
            'no_tlp' => 'required|string|max:20',
            'jurusan_id' => 'required|exists:jurusans,id',
            'tahun_mulai' => 'required|integer',
            'tahun_lulus' => 'required|integer',
            'alamat_jalan' => 'required|string|max:255',
            'alamat_rt' => 'required|integer',
            'alamat_rw' => 'required|integer',
            'alamat_desa' => 'required|string|max:255',
            'alamat_kelurahan' => 'required|string|max:255',
            'alamat_kecamatan' => 'required|string|max:255',
            'alamat_kode_pos' => 'required|numeric',
            'tempat_lahir' => 'required|string|max:255',
        ];
    }

    private function kuliahRules(): array
    {
        return [
            'kuliahs' => 'array',
            'kuliahs.*.nama_kampus' => 'required|string',
            'kuliahs.*.prodi' => 'required|string',
            'kuliahs.*.tahun_masuk' => 'required|digits:4',
            'kuliahs.*.tahun_lulus' => 'nullable|digits:4',
            'kuliahs.*.sesuai_jurusan' => 'required|boolean',
            'kuliahs.*.jalur_masuk_kuliah_id' => 'required|exists:jalur_masuk_kuliahs,id',
        ];
    }

    private function kerjaRules(): array
    {
        return [
            'kerjas' => 'array',
            'kerjas.*.nama_perusahaan' => 'required|string',
            'kerjas.*.alamat_perusahaan' => 'required|string',
            'kerjas.*.tgl_mulai' => 'required|date',
            'kerjas.*.tgl_selesai' => 'required|date',
            'kerjas.*.sesuai_jurusan' => 'required|boolean',
            'kerjas.*.jabatan' => 'required|string',
            'kerjas.*.masa_tunggu_kerja_id' => 'required|exists:masa_tunggu_kerjas,id',
            'kerjas.*.jenis_perusahaan_id' => 'required|exists:jenis_perusahaans,id',
            'kerjas.*.durasi_kerja_id' => 'required|exists:durasi_kerjas,id',
            'kerjas.*.range_gaji_id' => 'required|exists:range_gajis,id',
        ];
    }

    private function usahaRules(): array
    {
        return [
            'usahas' => 'array',
            'usahas.*.tgl_mulai' => 'required|date',
            'usahas.*.tgl_selesai' => 'required|date',
            'usahas.*.nama_perusahaan' => 'required|string',
            'usahas.*.bidang' => 'required|string',
            'usahas.*.jml_karyawan' => 'nullable|integer',
            'usahas.*.sesuai_jurusan' => 'required|boolean',
            'usahas.*.kepemilikan_usaha_id' => 'required|exists:kepemilikan_usahas,id',
            'usahas.*.range_laba_id' => 'required|exists:range_labas,id',
        ];
    }
}
