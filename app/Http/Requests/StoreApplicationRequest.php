<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'applicant_id' => ['required', 'exists:applicants,id'],
            // nullable لأن المتقدم قد يسجل في قائمة الانتظار العامة دون تحديد شاغر
            'job_request_id' => ['nullable', 'exists:job_requests,id'],
        ];
    }
}
