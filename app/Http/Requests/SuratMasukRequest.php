<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SuratMasukRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'tanggal_surat' => 'required|date',
            'tanggal_terima' => 'required|date',
            'pengirim' => 'required|string|max:255',
            'perihal' => 'required|string|max:500',
            'ringkasan' => 'nullable|string',
        ];

        if ($this->isMethod('post')) {
            $rules['file'] = 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120';
        } else {
            $rules['file'] = 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'tanggal_surat.required' => 'Tanggal surat harus diisi.',
            'tanggal_terima.required' => 'Tanggal terima harus diisi.',
            'pengirim.required' => 'Pengirim harus diisi.',
            'perihal.required' => 'Perihal harus diisi.',
            'file.mimes' => 'File harus berupa PDF, DOC, DOCX, JPG, JPEG, atau PNG.',
            'file.max' => 'Ukuran file maksimal 5MB.',
        ];
    }
}