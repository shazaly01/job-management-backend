<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
        return [
            'id' => $this->id,

            // القاعدة الذهبية DECIMAL(18,0) لضمان عدم ضياع الأرقام في جافاسكربت
            'RequestNo' => (string) $this->RequestNo,

            // الروابط الجديدة
            'slug' => $this->slug,

            /** * رابط التقديم المباشر:
             * نقوم بتركيبه هنا لكي يظهر للمدير في الواجهة ويقوم بنسخه بضغطة زر
             * قمت باستخدام config('app.url') لجلب رابط موقعك تلقائياً
             */
            'ApplyLink' => "{$frontendUrl}/apply/{$this->slug}",

            'RequiredDegreeLevel' => $this->RequiredDegreeLevel,
            'RequiredMajor' => $this->RequiredMajor,
            'RequiredYearsOfExperience' => $this->RequiredYearsOfExperience,

            // وصف الوظيفة الجديد
            'JobDescription' => $this->JobDescription,

            'Status' => $this->Status,

            // العلاقات
            'Department' => new DepartmentResource($this->whenLoaded('department')),

            // إحصائيات سريعة
            'ApplicationsCount' => $this->whenCounted('applications'),
            'InterviewsCount' => $this->whenCounted('interviews'), // أضفنا عدد المقابلات أيضاً

            'CreatedAt' => $this->created_at->format('Y-m-d H:i'),
            'UpdatedAt' => $this->updated_at->format('Y-m-d H:i'),
        ];
    }
}
