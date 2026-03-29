<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApplicantExperience;
use App\Models\Applicant;
use App\Http\Requests\StoreApplicantExperienceRequest;
use App\Http\Requests\UpdateApplicantExperienceRequest;
use App\Http\Resources\Api\ApplicantExperienceResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;

class ApplicantExperienceController extends Controller
{

    /**
     * عرض قائمة بجميع الخبرات المسجلة في النظام (للمسؤولين)
     * مع دعم التحميل المسبق لبيانات المتقدم لتجنب بطء الاستعلامات.
     */
    public function index(): AnonymousResourceCollection
    {
        $experiences = ApplicantExperience::with('applicant')->latest()->paginate(15);
        return ApplicantExperienceResource::collection($experiences);
    }

    /**
     * إضافة خبرة عملية جديدة لملف متقدم معين.
     */
    public function store(StoreApplicantExperienceRequest $request): ApplicantExperienceResource
    {
        // جلب المتقدم للتحقق من وجوده وصلاحية التعديل عليه
        $applicant = Applicant::findOrFail($request->applicant_id);

        // التحقق من أن المستخدم يملك صلاحية تحديث بيانات هذا المتقدم
        $this->authorize('update', $applicant);

        $experience = ApplicantExperience::create($request->validated());

        return new ApplicantExperienceResource($experience);
    }

    /**
     * عرض تفاصيل خبرة مهنية محددة.
     */
    public function show(ApplicantExperience $applicantExperience): ApplicantExperienceResource
    {
        $applicantExperience->load('applicant');
        return new ApplicantExperienceResource($applicantExperience);
    }

    /**
     * تحديث بيانات خبرة عملية سابقة.
     */
    public function update(UpdateApplicantExperienceRequest $request, ApplicantExperience $applicantExperience): ApplicantExperienceResource
    {
        // التحقق من أن المستخدم يملك صلاحية التعديل على المتقدم صاحب هذه الخبرة
        $this->authorize('update', $applicantExperience->applicant);

        $applicantExperience->update($request->validated());

        return new ApplicantExperienceResource($applicantExperience);
    }

    /**
     * حذف خبرة عملية (Soft Delete).
     */
    public function destroy(ApplicantExperience $applicantExperience): JsonResponse
    {
        // التحقق من الصلاحية قبل الحذف
        $this->authorize('update', $applicantExperience->applicant);

        $applicantExperience->delete();

        return response()->json([
            'status' => true,
            'message' => 'تم حذف الخبرة العملية بنجاح'
        ], 200);
    }

    /**
     * جلب كافة الخبرات العملية الخاصة بمتقدم معين (Useful for Profile View).
     * * @param int $applicantId
     * @return AnonymousResourceCollection
     */
    public function getByApplicant($applicantId): AnonymousResourceCollection
    {
        $experiences = ApplicantExperience::where('applicant_id', $applicantId)
            ->orderBy('StartDate', 'desc')
            ->get();

        return ApplicantExperienceResource::collection($experiences);
    }
}
