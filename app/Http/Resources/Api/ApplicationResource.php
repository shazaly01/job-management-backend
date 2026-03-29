<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
{
   public function toArray(Request $request): array
{
    return [
        'id' => $this->id,
        'TransactionNo' => (string) $this->TransactionNo,
        'ApplicationStatus' => $this->ApplicationStatus,

        // ✅ هنا يكمن السر: نمرر بيانات المتقدم مع تفاصيله المهنية
        'Applicant' => $this->applicant ? [
            'id' => $this->applicant->id,
            // بيانات المدينة
            'City' => $this->applicant->city?->Name,

            // ✅ تمرير المصفوفات المهنية التي نحتاجها للتوظيف الأعمى
            'Qualifications' => $this->applicant->qualifications,
            'Skills' => $this->applicant->skills,
            'Experiences' => $this->applicant->experiences,

            // إضافة مسمى وظيفي سريع من واقع الخبرة
            'CurrentJobTitle' => $this->applicant->experiences?->first()?->JobTitle ?? 'متقدم عام',
            'TotalYears' => $this->applicant->experiences?->count() ?? 0,
        ] : null,

        'JobRequest' => new JobRequestResource($this->whenLoaded('jobRequest')),
        'SubmittedAt' => $this->created_at->format('Y-m-d H:i'),
    ];
}
}
