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
        return [
            // NISN now divalidasi langsung unik di tabel nisns; akan auto dibuat bila belum ada
            'nisn_id' => ['required', 'gt:0', Rule::unique('alumnis', 'nisn_id')->ignore($this->route('alumni'))],
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'tgl_lahir' => 'required|date',
            'email' =>  ['required', 'email', Rule::unique('alumnis', 'email')->ignore($this->route('alumni'))],
            'no_tlp' => 'required|string|max:20',
            'jurusan_id' => 'required|exists:jurusans,id',
            'tahun_mulai' => 'required|integer|min:1900|max:' . (date('Y') + 10),
            'tahun_lulus' => 'required|integer|min:1900|max:' . (date('Y') + 10),
            'alamat_jalan' => 'required|string|max:255',
            'alamat_rt' => 'required|integer',
            'alamat_rw' => 'required|integer',
            'alamat_desa' => 'required|string|max:255',
            'alamat_kelurahan' => 'required|string|max:255',
            'alamat_kecamatan' => 'required|string|max:255',
            'alamat_kode_pos' => 'required|integer',
            'tempat_lahir' => 'required|string|max:255',
        ];
    }
}
