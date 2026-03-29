<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'Name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('cities', 'Name')->ignore($this->city)->whereNull('deleted_at')
            ],
            'IsActive' => ['sometimes', 'boolean'],
        ];
    }
}
