<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // الملف اختياري عند التحديث (قد يرغب بتحديث اسم المستند فقط)
            'file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'DocumentType' => ['sometimes', 'required', 'string', 'max:100'],
        ];
    }
}
