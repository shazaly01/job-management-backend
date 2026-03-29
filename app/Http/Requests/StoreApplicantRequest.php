<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApplicantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

 public function rules(): array
{
    return [
        // ✅ إزالة 'unique' لكي يتم التحديث بدل إرجاع خطأ
        'NationalID' => ['required', 'numeric', 'digits_between:10,18'],
        'FirstName' => ['required', 'string', 'max:100'],
        'LastName' => ['required', 'string', 'max:100'],
        'Email' => ['nullable', 'email', 'max:255'],
        'PhoneNumber' => ['nullable', 'string', 'max:20'],
        'city_id' => ['required', 'exists:cities,id'],
        'ApplicationSource' => ['required', 'in:Online,Manual'],
        'IsActive' => ['sometimes', 'boolean'],
    ];
}
}
