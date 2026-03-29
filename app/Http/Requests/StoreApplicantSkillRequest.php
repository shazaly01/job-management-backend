<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApplicantSkillRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'applicant_id' => ['required', 'exists:applicants,id'],
            'SkillName' => ['required', 'string', 'max:255'],
            'ProficiencyLevel' => ['required', 'in:Beginner,Intermediate,Advanced,Expert'],
        ];
    }
}
