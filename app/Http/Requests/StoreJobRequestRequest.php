<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // 1. رقم الطلب (مهم لتوليد الـ Slug وقاعدتك DECIMAL 18,0)
            'RequestNo' => ['nullable', 'numeric', 'unique:job_requests,RequestNo'],

            'department_id' => ['required', 'exists:departments,id'],
            'RequiredDegreeLevel' => ['required', 'string', 'max:100'],
            'RequiredMajor' => ['nullable', 'string', 'max:255'],
            'RequiredYearsOfExperience' => ['required', 'integer', 'min:0', 'max:50'],

            // 2. وصف الوظيفة (الذي ظهر في لقطة الشاشة)
            'JobDescription' => ['nullable', 'string'],

            'Status' => ['nullable', 'in:Open,Closed,Fulfilled'],
        ];
    }

    /**
     * تخصيص أسماء الحقول في رسائل الخطأ لتكون واضحة للمستخدم
     */
    public function attributes(): array
    {
        return [
            'RequestNo' => 'رقم طلب التوظيف',
            'department_id' => 'الإدارة التابع لها',
            'RequiredMajor' => 'التخصص المطلوب',
            'JobDescription' => 'وصف الوظيفة والمسؤوليات',
        ];
    }
}
