<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApplicantSkill;
use App\Models\Applicant;
use App\Http\Requests\StoreApplicantSkillRequest;
use App\Http\Requests\UpdateApplicantSkillRequest;
use App\Http\Resources\Api\ApplicantSkillResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;

class ApplicantSkillController extends Controller
{

    /**
     * عرض قائمة بجميع المهارات المسجلة في النظام (لأغراض إدارية).
     * يتم تحميل بيانات المتقدم لتوفير استعلامات قاعدة البيانات.
     */
    public function index(): AnonymousResourceCollection
    {
        $skills = ApplicantSkill::with('applicant')->latest()->paginate(15);
        return ApplicantSkillResource::collection($skills);
    }

    /**
     * إضافة مهارة جديدة لملف متقدم.
     * يتم التحقق من وجود المتقدم وصلاحية المستخدم لإضافة بيانات له.
     */
    public function store(StoreApplicantSkillRequest $request): ApplicantSkillResource
    {
        // جلب المتقدم للتأكد من وجوده
        $applicant = Applicant::findOrFail($request->applicant_id);

        // التحقق من صلاحية المستخدم (هل يحق له تعديل بيانات هذا المتقدم؟)
        $this->authorize('update', $applicant);

        $skill = ApplicantSkill::create($request->validated());

        return new ApplicantSkillResource($skill);
    }

    /**
     * عرض تفاصيل مهارة معينة.
     */
    public function show(ApplicantSkill $applicantSkill): ApplicantSkillResource
    {
        $applicantSkill->load('applicant');
        return new ApplicantSkillResource($applicantSkill);
    }

    /**
     * تحديث بيانات مهارة موجودة (مثل تغيير مستوى الإجادة).
     */
    public function update(UpdateApplicantSkillRequest $request, ApplicantSkill $applicantSkill): ApplicantSkillResource
    {
        // التحقق من الصلاحية: يجب أن يملك المستخدم حق تعديل المتقدم المرتبط بهذه المهارة
        $this->authorize('update', $applicantSkill->applicant);

        $applicantSkill->update($request->validated());

        return new ApplicantSkillResource($applicantSkill);
    }

    /**
     * حذف مهارة من ملف المتقدم (الحذف المرن Soft Delete).
     */
    public function destroy(ApplicantSkill $applicantSkill): JsonResponse
    {
        // التحقق من الصلاحية قبل الحذف
        $this->authorize('update', $applicantSkill->applicant);

        $applicantSkill->delete();

        return response()->json([
            'status' => true,
            'message' => 'تم إزالة المهارة من ملف المتقدم بنجاح'
        ], 200);
    }

    /**
     * جلب كافة المهارات الخاصة بمتقدم معين.
     * @param int $applicantId
     * @return AnonymousResourceCollection
     */
    public function getByApplicant($applicantId): AnonymousResourceCollection
    {
        $skills = ApplicantSkill::where('applicant_id', $applicantId)
            ->orderBy('SkillName', 'asc')
            ->get();

        return ApplicantSkillResource::collection($skills);
    }
}
