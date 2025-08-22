<?php

namespace App\Http\Requests;

class AlumniLoginRequest extends ApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Now using NISN for alumni login instead of email
            'nisn' => 'required|digits_between:8,15',
            'password' => 'required'
        ];
    }
}
