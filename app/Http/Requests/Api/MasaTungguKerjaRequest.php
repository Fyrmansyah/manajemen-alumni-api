<?php

namespace App\Http\Requests\Api;

use Illuminate\Validation\Rule;

class MasaTungguKerjaRequest extends ApiFormRequest
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
            'value' => [
                'required',
                Rule::unique('masa_tunggu_kerjas')->ignore($this->route('masa_tunggu_kerja'))
            ]
        ];
    }
}
