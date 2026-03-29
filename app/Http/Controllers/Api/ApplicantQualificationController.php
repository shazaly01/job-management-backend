<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApplicantQualification;
use App\Http\Requests\StoreApplicantQualificationRequest;
use App\Http\Requests\UpdateApplicantQualificationRequest;
use App\Http\Resources\Api\ApplicantQualificationResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ApplicantQualificationController extends Controller
{


    /**
     * عرض كل المؤهلات (غالباً لغرض الفهرسة العامة)
     */
    public function index(): AnonymousResourceCollection
    {
        $qualifications = ApplicantQualification::with('applicant')->paginate(15);
        return ApplicantQualificationResource::collection($qualifications);
    }

    /**
     * إضافة مؤهل علمي جديد لمتقدم
     */
    public function store(StoreApplicantQualificationRequest $request): ApplicantQualificationResource
    {
        // 1. جلب المتقدم أولاً (كما فعلت في متحكم الخبرات)
        $applicant = \App\Models\Applicant::findOrFail($request->applicant_id);

        // 2. التحقق من الصلاحية بتمرير كائن المتقدم كاملاً وليس رقمه
        $this->authorize('update', $applicant);

        $qualification = ApplicantQualification::create($request->validated());

        return new ApplicantQualificationResource($qualification);
    }

    /**
     * عرض تفاصيل مؤهل معين
     */
    public function show(ApplicantQualification $applicantQualification): ApplicantQualificationResource
    {
        $applicantQualification->load('applicant');
        return new ApplicantQualificationResource($applicantQualification);
    }

    /**
     * تحديث بيانات مؤهل علمي
     */
    public function update(UpdateApplicantQualificationRequest $request, ApplicantQualification $applicantQualification): ApplicantQualificationResource
    {
        // التحقق من الصلاحية
        $this->authorize('update', $applicantQualification->applicant);

        $applicantQualification->update($request->validated());

        return new ApplicantQualificationResource($applicantQualification);
    }

    /**
     * حذف مؤهل علمي (Soft Delete)
     */
   public function destroy(ApplicantQualification $applicantQualification)
    {
        // تأكد من تحميل بيانات المتقدم المرتبط بهذا المؤهل أولاً
        $applicantQualification->loadMissing('applicant');

        // التحقق من الصلاحية
        $this->authorize('update', $applicantQualification->applicant);

        $applicantQualification->delete();

        return response()->json([
            'status' => true,
            'message' => 'تم حذف المؤهل العلمي بنجاح ونقله لسلة المحذوفات'
        ], 200);
    }

    /**
     * جلب كل مؤهلات متقدم معين بشكل مباشر
     */
    public function getByApplicant($applicantId): AnonymousResourceCollection
    {
        $qualifications = ApplicantQualification::where('applicant_id', $applicantId)->get();
        return ApplicantQualificationResource::collection($qualifications);
    }
}
