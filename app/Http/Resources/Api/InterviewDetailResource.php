<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class InterviewDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'interview_id' => $this->interview_id,
            'application_id' => $this->application_id,

            // تنسيق الوقت ليظهر بشكل جميل (مثال: 09:30)
            'InterviewTime' => $this->InterviewTime ? Carbon::parse($this->InterviewTime)->format('H:i') : null,

            'EvaluationScore' => $this->EvaluationScore,
            'Result' => $this->Result,
            'Notes' => $this->Notes,

            // جلب بيانات المرشح (السيرة الذاتية، الاسم، إلخ) عبر علاقة الـ Application
            'Application' => new ApplicationResource($this->whenLoaded('application')),
        ];
    }
}
