<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // استيراد كلاس القواعد للتأكد من الـ Unique

class UpdateJobRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // 1. رقم الطلب: نستخدم Rule::unique لتجاهل السجل الحالي أثناء الفحص
            'RequestNo' => [
            'nullable',
            'numeric',
            Rule::unique('job_requests')->ignore($this->route('job_request'))
        ],

            'department_id' => ['sometimes', 'required', 'exists:departments,id'],
            'RequiredDegreeLevel' => ['sometimes', 'required', 'string', 'max:100'],
            'RequiredMajor' => ['nullable', 'string', 'max:255'],
            'RequiredYearsOfExperience' => ['sometimes', 'required', 'integer', 'min:0', 'max:50'],

            // 2. وصف الوظيفة (للسماح بتعديله)
            'JobDescription' => ['nullable', 'string'],

            'Status' => ['sometimes', 'required', 'in:Open,Closed,Fulfilled'],
        ];
    }

    /**
     * تخصيص أسماء الحقول
     */
    public function attributes(): array
    {
        return [
            'RequestNo' => 'رقم طلب التوظيف',
            'department_id' => 'الإدارة التابع لها',
            'JobDescription' => 'وصف الوظيفة',
        ];
    }
}
