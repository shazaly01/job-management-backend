<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // التحقق من الملف المرفوع (حجم أقصى 5 ميجابايت مثلاً)
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'name' => ['nullable', 'string', 'max:255'],
            'DocumentType' => ['nullable', 'string', 'max:100'],

            // بيانات العلاقة متعددة الأشكال
            'documentable_id' => ['required', 'integer'],
            'documentable_type' => ['required', 'string'], // مثل App\Models\Applicant
        ];
    }
}
