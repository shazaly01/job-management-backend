<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // غالباً في التحديث يتم تغيير حالة الطلب فقط
            'ApplicationStatus' => ['sometimes', 'required', 'in:Pending,Shortlisted,Interview,Accepted,Rejected'],
            'job_request_id' => ['sometimes', 'nullable', 'exists:job_requests,id'],
        ];
    }
}
