<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InterviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'job_request_id' => $this->job_request_id,

            // تحويل كود الموظف إلى نص لضمان عدم فقدان أي أرقام من الـ DECIMAL(18,0) في الجافاسكربت
            'EmpCode' => (string) $this->EmpCode,

            // التاريخ فقط لأن الوقت أصبح في التفاصيل
            'InterviewDate' => $this->InterviewDate ? $this->InterviewDate->format('Y-m-d') : null,

            'Location' => $this->Location,
            'Status' => $this->Status,
            'Notes' => $this->Notes,

            // 1. جلب بيانات طلب الوظيفة (إن وجدت)
            'JobRequest' => $this->whenLoaded('jobRequest', function () {
                return [
                    'id' => $this->jobRequest->id,
                    'RequestNo' => (string) $this->jobRequest->RequestNo,
                    'RequiredMajor' => $this->jobRequest->RequiredMajor,
                    // يمكنك استدعاء JobRequestResource هنا إذا كان لديك واحد
                ];
            }),

            // 2. 🚨 جلب مصفوفة المرشحين (التفاصيل)
            'Details' => InterviewDetailResource::collection($this->whenLoaded('details')),
        ];
    }
}
