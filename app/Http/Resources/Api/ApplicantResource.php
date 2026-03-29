<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ApplicantNo' => (string) $this->ApplicantNo,

            // استخدام whenHas لعدم إرجاع الحقل إذا كنا نستخدم التوظيف المجهول (Blind Scope)
            'NationalID' => $this->whenHas('NationalID', fn() => (string) $this->NationalID),
            'ReferenceCode' => $this->whenHas('ReferenceCode', fn() => (string) $this->ReferenceCode),
            'FirstName' => $this->whenHas('FirstName'),
            'LastName' => $this->whenHas('LastName'),
            'Email' => $this->whenHas('Email'),
            'PhoneNumber' => $this->whenHas('PhoneNumber'),

            'ApplicationSource' => $this->ApplicationSource,
            'IsActive' => $this->whenHas('IsActive', fn() => (bool) $this->IsActive),

            // جلب العلاقات إذا كانت محملة (Loaded)
            'City' => new CityResource($this->whenLoaded('city')),
            'Qualifications' => ApplicantQualificationResource::collection($this->whenLoaded('qualifications')),
            'Experiences' => ApplicantExperienceResource::collection($this->whenLoaded('experiences')),
            'Skills' => ApplicantSkillResource::collection($this->whenLoaded('skills')),
            'Documents' => DocumentResource::collection($this->whenLoaded('documents')),

            'RegisteredAt' => $this->whenHas('created_at', fn() => $this->created_at->format('Y-m-d H:i')),
        ];
    }
}
