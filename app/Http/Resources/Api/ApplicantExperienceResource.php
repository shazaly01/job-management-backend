<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicantExperienceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'JobTitle' => $this->JobTitle,
            'CompanyName' => $this->CompanyName,
            // تنسيق التاريخ ليكون Y-m-d
            'StartDate' => $this->StartDate ? $this->StartDate->format('Y-m-d') : null,
            // إذا كان EndDate فارغاً، فهذا يعني أنه "لا يزال على رأس عمله"
            'EndDate' => $this->EndDate ? $this->EndDate->format('Y-m-d') : 'Present',
            'JobDescription' => $this->JobDescription,

            'Applicant' => new ApplicantResource($this->whenLoaded('applicant')),
        ];
    }
}
