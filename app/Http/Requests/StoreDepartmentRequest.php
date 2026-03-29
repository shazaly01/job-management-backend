<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // استخدام numeric مع تحديد عدد الخانات ليتناسب مع DECIMAL(18,0)
            'DeptCode' => ['sometimes', 'numeric'],
            'Name' => ['required', 'string', 'max:255'],
            'IsActive' => ['sometimes', 'boolean'],
        ];
    }
}
