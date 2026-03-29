<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobRequest;
use App\Http\Requests\StoreJobRequestRequest;
use App\Http\Requests\UpdateJobRequestRequest;
use App\Http\Resources\Api\JobRequestResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class JobRequestController extends Controller
{
    /**
     * تأمين المتحكم وربطه بالسياسة الأمنية (JobRequestPolicy).
     */
    public function __construct()
    {
        // نستثني دالة عرض الطلب عبر الرابط (showBySlug) من المصادقة لأنها للمتقدمين العامين
        $this->authorizeResource(JobRequest::class, 'job_request', [
            'except' => ['showBySlug']
        ]);
    }

    /**
     * عرض قائمة بكافة طلبات التوظيف (للمدراء).
     */
    public function index(): AnonymousResourceCollection
    {
        $jobRequests = JobRequest::with(['department'])
            ->withCount(['applications', 'interviews'])
            ->latest()
            ->paginate(15);

        return JobRequestResource::collection($jobRequests);
    }

    /**
     * إنشاء طلب توظيف جديد مع توليد رقم الطلب والـ Slug تلقائياً.
     */
   public function store(StoreJobRequestRequest $request): JobRequestResource
{
    // الموديل سيتكفل بتوليد الرقم والـ Slug تلقائياً بفضل دالة booted
    $jobRequest = JobRequest::create($request->validated());

    return new JobRequestResource($jobRequest);
}

    /**
     * عرض تفاصيل طلب توظيف للمدراء (مع المتقدمين).
     */
    public function show(JobRequest $jobRequest): JobRequestResource
    {
        $jobRequest->load(['department', 'applications.applicant']);
        return new JobRequestResource($jobRequest);
    }

    /**
     * دالة سحرية: عرض تفاصيل الوظيفة للمتقدمين عبر الرابط (Slug).
     * هذه الدالة لا تحتاج تسجيل دخول (يجب وضعها في المسارات العامة).
     */
    public function showBySlug($slug): JobRequestResource
    {
        // نبحث عن الوظيفة المفتوحة فقط باستخدام الـ Slug
        $jobRequest = JobRequest::where('slug', $slug)
            ->where('Status', 'Open')
            ->with('department')
            ->firstOrFail();

        return new JobRequestResource($jobRequest);
    }

    /**
     * تحديث بيانات الطلب.
     */
    public function update(UpdateJobRequestRequest $request, JobRequest $jobRequest): JobRequestResource
    {
        $jobRequest->update($request->validated());

        return new JobRequestResource($jobRequest);
    }

    /**
     * حذف طلب توظيف (Soft Delete).
     */
    public function destroy(JobRequest $jobRequest): JsonResponse
    {
        $jobRequest->delete();

        return response()->json([
            'status' => true,
            'message' => 'تم نقل طلب التوظيف إلى الأرشيف بنجاح'
        ], 200);
    }

    /**
     * جلب الطلبات المفتوحة فقط (لاستخدامها في القوائم المنسدلة).
     */
    public function getOpenRequests(): AnonymousResourceCollection
    {
        $openRequests = JobRequest::where('Status', 'Open')
            ->with('department')
            ->latest()
            ->get();

        return JobRequestResource::collection($openRequests);
    }
}
