<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInterviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // بيانات الرأس (يمكن تحديثها)
            'EmpCode'       => ['sometimes', 'required', 'numeric', 'digits_between:1,18'],
            'InterviewDate' => ['sometimes', 'required', 'date'],
            'Location'      => ['nullable', 'string', 'max:255'],
            'Status'        => ['sometimes', 'required', 'in:Scheduled,Completed,Cancelled'],
            'Notes'         => ['nullable', 'string'],

            // بيانات التفاصيل (تحديث المرشحين وإدخال التقييم)
            'candidates'    => ['sometimes', 'array'],

            // في حالة التحديث، قد نرسل ID التفاصيل لمعرفته
            'candidates.*.id'              => ['nullable', 'exists:interview_details,id'],
            'candidates.*.application_id'  => ['required_with:candidates', 'exists:applications,id'],
            'candidates.*.InterviewTime'   => ['required_with:candidates', 'date_format:H:i'],

            // هذه الحقول الجديدة للتقييم
            'candidates.*.EvaluationScore' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'candidates.*.Result'          => ['nullable', 'in:Passed,Failed,Pending'],
            'candidates.*.Notes'           => ['nullable', 'string'], // ملاحظات على أداء المرشح
        ];
    }
}
