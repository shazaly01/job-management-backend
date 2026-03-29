<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicantQualificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'DegreeLevel' => $this->DegreeLevel,
            'Major' => $this->Major,
            'GraduationYear' => $this->GraduationYear,
            'UniversityOrInstitute' => $this->UniversityOrInstitute,
            'GPA_or_Grade' => $this->GPA_or_Grade,

            // جلب بيانات المتقدم الأساسية إذا تم طلبها في الـ Controller (eager loaded)
            'Applicant' => new ApplicantResource($this->whenLoaded('applicant')),
        ];
    }
}
