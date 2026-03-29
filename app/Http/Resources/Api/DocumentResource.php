<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'DocumentType' => $this->DocumentType,
            // الرابط الموقّع والآمن الذي قمنا ببرمجته في الـ Accessor
            'FileUrl' => $this->url,
            'FileFormat' => $this->FileFormat,

            'UploadedAt' => $this->created_at->format('Y-m-d H:i'),
        ];
    }
}
