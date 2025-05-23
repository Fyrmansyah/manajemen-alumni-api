<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class CreateJurusanRequest extends ApiFormRequest
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
            'tgl_berdiri' => 'required'
        ];
    }


    protected function prepareForValidation()
    {
        if ($this->has('tgl_berdiri')) {
            $tglBerdiri = Carbon::parse($this->tgl_berdiri)->format('Y-m-d H:i:s');

            $this->merge([
                'tgl_berdiri' => $tglBerdiri,
            ]);
        }
    }
}
