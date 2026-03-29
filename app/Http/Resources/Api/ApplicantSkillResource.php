<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicantSkillResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'SkillName' => $this->SkillName,
            'ProficiencyLevel' => $this->ProficiencyLevel,

            'Applicant' => new ApplicantResource($this->whenLoaded('applicant')),
        ];
    }
}
