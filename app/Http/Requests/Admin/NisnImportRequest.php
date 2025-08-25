<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class NisnImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin') !== null; // guard admin
    }

    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:csv,txt',
        ];
    }
}
