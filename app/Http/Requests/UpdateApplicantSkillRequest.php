<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicantSkillRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'applicant_id' => ['sometimes', 'exists:applicants,id'],
            'SkillName' => ['sometimes', 'required', 'string', 'max:255'],
            'ProficiencyLevel' => ['sometimes', 'required', 'in:Beginner,Intermediate,Advanced,Expert'],
        ];
    }
}
