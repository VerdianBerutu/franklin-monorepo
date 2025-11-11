<?php

namespace App\Http\Requests\Upload;

use Illuminate\Foundation\Http\FormRequest;

class StoreUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Upload::class);
    }

    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'max:10240', // 10MB
                'mimes:pdf,jpg,jpeg,png,docx,xlsx'
            ],
            'title' => 'required|string|max:255',
            'category' => 'required|in:laporan,sertifikat,bukti',
            'description' => 'nullable|string|max:500',
            'uploaded_at' => 'nullable|date'
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'File wajib diupload',
            'file.max' => 'Ukuran file maksimal 10MB',
            'file.mimes' => 'Tipe file harus: pdf, jpg, jpeg, png, docx, xlsx',
            'title.required' => 'Judul dokumen wajib diisi',
            'category.required' => 'Kategori wajib dipilih',
            'category.in' => 'Kategori tidak valid'
        ];
    }
}