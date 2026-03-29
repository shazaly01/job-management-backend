<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            // تحويل الرقم الطويل إلى نص لحماية الدقة في الجافاسكريبت
            'DeptCode' => (string) $this->DeptCode,
            'Name' => $this->Name,
            'IsActive' => (bool) $this->IsActive,
        ];
    }
}
