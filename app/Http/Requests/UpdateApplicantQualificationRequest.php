<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicantQualificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // غالباً لا يتم تغيير applicant_id عند التحديث، لكن نضعه للحماية
            'applicant_id' => ['sometimes', 'exists:applicants,id'],
            'DegreeLevel' => ['sometimes', 'required', 'string', 'max:100'],
            'Major' => ['sometimes', 'required', 'string', 'max:255'],
            'GraduationYear' => ['sometimes', 'required', 'integer', 'digits:4', 'min:1950', 'max:' . (date('Y') + 5)],
            'UniversityOrInstitute' => ['sometimes', 'required', 'string', 'max:255'],
            'GPA_or_Grade' => ['nullable', 'string', 'max:50'],
        ];
    }
}
