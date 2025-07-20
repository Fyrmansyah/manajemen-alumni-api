<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplicationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'cover_letter' => 'required|string|max:2000',
            'cv_file' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'cover_letter.required' => 'Surat lamaran wajib diisi',
            'cover_letter.max' => 'Surat lamaran maksimal 2000 karakter',
            'cv_file.mimes' => 'File CV harus berformat PDF, DOC, atau DOCX',
            'cv_file.max' => 'Ukuran file CV maksimal 2MB',
        ];
    }
}
