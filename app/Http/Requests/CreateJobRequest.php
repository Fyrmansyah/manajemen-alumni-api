<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateJobRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'job_type' => 'required|in:Full Time,Part Time,Kontrak,Freelance,Magang',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'deadline' => 'nullable|date|after:today',
            'status' => 'in:draft,active',
            'is_published' => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Judul pekerjaan wajib diisi',
            'description.required' => 'Deskripsi pekerjaan wajib diisi',
            'location.required' => 'Lokasi pekerjaan wajib diisi',
            'job_type.required' => 'Tipe pekerjaan wajib dipilih',
            'job_type.in' => 'Tipe pekerjaan tidak valid',
            'salary_max.gte' => 'Gaji maksimum harus lebih besar atau sama dengan gaji minimum',
            'deadline.after' => 'Tanggal deadline harus setelah hari ini',
        ];
    }
}
