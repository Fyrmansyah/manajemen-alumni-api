<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

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
            'nama' => 'required',
            'tgl_lahir' => 'required',
            'tahun_mulai' => 'required',
            'tahun_lulus' => 'required',
            'no_tlp' => 'required',
            'email' => 'required',
            'alamat' => 'required',
            'jurusan_id' => 'required',
        ];
    }
}
