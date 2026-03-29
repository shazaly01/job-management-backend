<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateApplicantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // استخراج الـ id الخاص بالمتقدم من مسار الرابط (Route)
        $applicantId = $this->route('applicant') ? $this->route('applicant')->id : null;

        return [
            'NationalID' => [
                'required',
                'numeric',
                'digits_between:10,18',
                Rule::unique('applicants', 'NationalID')->ignore($applicantId)->whereNull('deleted_at')
            ],
            'FirstName' => ['required', 'string', 'max:100'],
            'LastName' => ['required', 'string', 'max:100'],
            'Email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('applicants', 'Email')->ignore($applicantId)->whereNull('deleted_at')
            ],
            'PhoneNumber' => ['nullable', 'string', 'max:20'],
            'city_id' => ['required', 'exists:cities,id'],
            'ApplicationSource' => ['required', 'in:Online,Manual'],
            'IsActive' => ['sometimes', 'boolean'],
        ];
    }
}
