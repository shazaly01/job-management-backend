<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'DeptCode' => [
                'required',
                'numeric',
                'digits_between:1,18',
                Rule::unique('departments', 'DeptCode')->ignore($this->department)->whereNull('deleted_at')
            ],
            'Name' => ['required', 'string', 'max:255'],
            'IsActive' => ['sometimes', 'boolean'],
        ];
    }
}
