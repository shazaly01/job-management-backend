<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Http\Requests\StoreApplicationRequest;
use App\Http\Requests\UpdateApplicationRequest;
use App\Http\Resources\Api\ApplicationResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    /**
     * تأمين المتحكم وربطه بالسياسة الأمنية (ApplicationPolicy).
     * سيتحقق لارافيل من صلاحيات المستخدم لكل دالة تلقائياً.
     */
    public function __construct()
    {
        $this->authorizeResource(Application::class, 'application');
    }

    /**
     * عرض قائمة بكافة حركات التقديم في النظام.
     * يتم جلب بيانات المتقدم وطلب التوظيف المرتبط به دفعة واحدة.
     */
   public function index(Request $request): AnonymousResourceCollection
{
    $query = Application::with(['applicant', 'jobRequest.department']);

    // 1. البحث النصي (رقم الحركة، أو اسم المتقدم/الرقم الوطني، أو رقم طلب التوظيف)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('TransactionNo', 'like', "%{$search}%")
              ->orWhereHas('applicant', function($qApp) use ($search) {
                  $qApp->where('FirstName', 'like', "%{$search}%")
                       ->orWhere('LastName', 'like', "%{$search}%")
                       ->orWhere('ApplicantNo', $search);
              })
              ->orWhereHas('jobRequest', function($qJob) use ($search) {
                  $qJob->where('RequestNo', $search);
              });
        });
    }

    // 2. الفلترة حسب حالة التقديم
    if ($request->filled('status')) {
        $query->where('ApplicationStatus', $request->status);
    }

    // 3. الفلترة حسب طلب توظيف معين (لو أردت عرض تقديمات وظيفة محددة)
    if ($request->filled('job_request_id')) {
        $query->where('job_request_id', $request->job_request_id);
    }

    $applications = $query->latest()->paginate(15);

    return ApplicationResource::collection($applications);
}
    /**
     * إنشاء حركة تقديم جديدة (ربط متقدم بطلب توظيف).
     * يتم توليد TransactionNo برمجياً لضمان الدقة المالية والإدارية.
     */
    public function store(StoreApplicationRequest $request): ApplicationResource
    {
        return DB::transaction(function () use ($request) {
            /**
             * توليد رقم الحركة (TransactionNo):
             * نستخدم (السنة + الشهر + اليوم + الساعة + الدقيقة + الثواني + 4 أرقام عشوائية)
             * النتيجة ستكون رقماً مكوناً من 18 خانة تقريباً، وهو مثالي لـ DECIMAL(18,0).
             */
            $generatedNo = now()->format('YmdHis') . rand(1000, 9999);

            $application = Application::create(array_merge(
                $request->validated(),
                [
                    'TransactionNo' => $generatedNo,
                    'ApplicationStatus' => 'Pending' // الحالة الافتراضية عند التقديم
                ]
            ));

            return new ApplicationResource($application);
        });
    }

    /**
     * عرض تفاصيل حركة تقديم محددة مع سجل المقابلات المرتبط بها.
     */
   public function show(Application $application): ApplicationResource
    {
        // تم إضافة تحميل المؤهلات، الخبرات، المهارات، والمستندات الخاصة بالمتقدم
        $application->load([
            'applicant.city',
            'applicant.qualifications',
            'applicant.experiences',
            'applicant.skills',
            'applicant.documents',
            'jobRequest.department'
        ]);

        return new ApplicationResource($application);
    }

    /**
     * تحديث حالة الطلب (مثلاً: الانتقال من Pending إلى Shortlisted أو Rejected).
     */
    public function update(UpdateApplicationRequest $request, Application $application): ApplicationResource
    {
        $application->update($request->validated());

        return new ApplicationResource($application);
    }

    /**
     * إلغاء حركة تقديم (Soft Delete).
     */
    public function destroy(Application $application): JsonResponse
    {
        $application->delete();

        return response()->json([
            'status' => true,
            'message' => 'تم إلغاء حركة التقديم بنجاح'
        ], 200);
    }

    /**
     * دالة إضافية: جلب طلبات تقديم خاصة بمتقدم معين.
     * @param int $applicantId
     */
    public function getByApplicant($applicantId): AnonymousResourceCollection
    {
        $applications = Application::where('applicant_id', $applicantId)
            ->with(['jobRequest.department'])
            ->latest()
            ->get();

        return ApplicationResource::collection($applications);
    }

    /**
     * دالة إضافية: جلب كافة المتقدمين لطلب توظيف محدد (فرز الإدارات).
     * @param int $jobRequestId
     */
   public function getByJobRequest($jobRequestId): AnonymousResourceCollection
{
    $applications = Application::where('job_request_id', $jobRequestId)
        // ✅ التعديل: جلب المتقدم مع كافة تفاصيله المهنية دفعة واحدة
        ->with([
            'applicant.city',
            'applicant.qualifications',
            'applicant.experiences',
            'applicant.skills'
        ])
        ->latest()
        ->get();

    return ApplicationResource::collection($applications);
}
}
