<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCompanyRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'company_name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'website' => 'nullable|url',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:jurusans,id',
            'established_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'company_size' => 'nullable|string',
            'contact_person' => 'required|string|max:255',
            'contact_person_phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    public function messages()
    {
        return [
            'company_name.required' => 'Nama perusahaan wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'phone.required' => 'Nomor telepon wajib diisi',
            'address.required' => 'Alamat wajib diisi',
            'website.url' => 'Format website tidak valid',
            'category_id.exists' => 'Kategori tidak valid',
            'established_year.min' => 'Tahun berdiri minimal 1900',
            'established_year.max' => 'Tahun berdiri maksimal ' . date('Y'),
            'contact_person.required' => 'Nama kontak person wajib diisi',
            'contact_person_phone.required' => 'Nomor telepon kontak person wajib diisi',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ];
    }
}
