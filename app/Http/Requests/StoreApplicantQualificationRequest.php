<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApplicantQualificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'applicant_id' => ['required', 'exists:applicants,id'],
            'DegreeLevel' => ['required', 'string', 'max:100'],
            'Major' => ['required', 'string', 'max:255'],
            'GraduationYear' => ['required', 'integer', 'digits:4', 'min:1950', 'max:' . (date('Y') + 5)],
            'UniversityOrInstitute' => ['required', 'string', 'max:255'],
            'GPA_or_Grade' => ['nullable', 'string', 'max:50'],
        ];
    }
}
