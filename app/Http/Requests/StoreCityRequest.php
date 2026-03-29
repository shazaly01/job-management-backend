<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // نتركها true لأننا سنستخدم الـ Policies في الـ Controller
    }

    public function rules(): array
    {
        return [
            'Name' => ['required', 'string', 'max:255', 'unique:cities,Name,NULL,id,deleted_at,NULL'],
            'IsActive' => ['sometimes', 'boolean'],
        ];
    }
}
