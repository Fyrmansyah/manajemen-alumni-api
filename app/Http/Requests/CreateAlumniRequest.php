<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAlumniRequest extends FormRequest
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
            'nisn' => 'required|string|max:20|unique:alumnis,nisn',
            'nama' => 'required|string|max:255',
            'nama_lengkap' => 'nullable|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'tgl_lahir' => 'required|date',
            'tanggal_lahir' => 'nullable|date',
            'email' => 'required|email|unique:alumnis,email',
            'password' => 'required|string|min:6',
            'no_tlp' => 'required|string|max:20',
            'phone' => 'nullable|string|max:20',
            'alamat' => 'required|string|max:500',
            'jurusan_id' => 'required|exists:jurusans,id',
            'tahun_mulai' => 'required|integer|min:1900|max:' . (date('Y') + 10),
            'tahun_lulus' => 'required|integer|min:1900|max:' . (date('Y') + 10),
            'tempat_kerja' => 'nullable|string|max:255',
            'jabatan_kerja' => 'nullable|string|max:255',
            'tempat_kuliah' => 'nullable|string|max:255',
            'prodi_kuliah' => 'nullable|string|max:255',
            'kesesuaian_kerja' => 'nullable|boolean',
            'kesesuaian_kuliah' => 'nullable|boolean',
            'pengalaman_kerja' => 'nullable|string',
            'keahlian' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_verified' => 'boolean',
        ];
    }
}
