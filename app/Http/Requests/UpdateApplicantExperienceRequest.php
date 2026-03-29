<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicantExperienceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'applicant_id' => ['sometimes', 'exists:applicants,id'],
            'JobTitle' => ['sometimes', 'required', 'string', 'max:255'],
            'CompanyName' => ['sometimes', 'required', 'string', 'max:255'],
            'StartDate' => ['sometimes', 'required', 'date', 'before_or_equal:today'],
            'EndDate' => ['nullable', 'date', 'after_or_equal:StartDate'],
            'JobDescription' => ['nullable', 'string'],
        ];
    }
}
