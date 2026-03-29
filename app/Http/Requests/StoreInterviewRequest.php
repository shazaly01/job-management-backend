<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInterviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // تأكد من وضع صلاحياتك هنا لاحقاً إذا لزم الأمر
    }

    public function rules(): array
    {
        return [
            // 1. بيانات "رأس الفاتورة" (الجلسة)
            'job_request_id' => ['required', 'exists:job_requests,id'],
            'EmpCode'        => ['required', 'numeric', 'digits_between:1,18'], // قاعدتك الخاصة
            'InterviewDate'  => ['required', 'date', 'after_or_equal:today'],
            'Location'       => ['nullable', 'string', 'max:255'],
            'Status'         => ['nullable', 'in:Scheduled,Completed,Cancelled'],
            'Notes'          => ['nullable', 'string'],

            // 2. بيانات "تفاصيل الفاتورة" (المرشحين) - يجب أن نرسل مصفوفة
            'candidates'     => ['required', 'array', 'min:1'], // يجب اختيار مرشح واحد على الأقل

            // التحقق من كل مرشح داخل المصفوفة
            'candidates.*.application_id' => ['required', 'exists:applications,id'],
            'candidates.*.InterviewTime'  => ['required', 'date_format:H:i'], // تأكيد أن الوقت بصيغة صحيحة مثل 09:30
        ];
    }

    public function messages(): array
    {
        return [
            'candidates.required' => 'يجب اختيار مرشح واحد على الأقل لهذه المقابلة.',
            'candidates.*.application_id.required' => 'رقم طلب التوظيف للمرشح مفقود.',
            'candidates.*.InterviewTime.required' => 'يجب تحديد وقت المقابلة لكل مرشح.',
            'candidates.*.InterviewTime.date_format' => 'صيغة الوقت غير صحيحة، يجب أن تكون (ساعة:دقيقة).',
        ];
    }
}
