<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApplicantExperienceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'applicant_id' => ['required', 'exists:applicants,id'],
            'JobTitle' => ['required', 'string', 'max:255'],
            'CompanyName' => ['required', 'string', 'max:255'],
            'StartDate' => ['required', 'date', 'before_or_equal:today'],
            // تاريخ النهاية يجب أن يكون بعد أو يساوي تاريخ البداية، ويمكن أن يكون فارغاً إذا كان مستمراً بالعمل
            'EndDate' => ['nullable', 'date', 'after_or_equal:StartDate'],
            'JobDescription' => ['nullable', 'string'],
        ];
    }
}
