<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Http\Requests\StoreApplicantRequest;
use App\Http\Requests\UpdateApplicantRequest;
use App\Http\Resources\Api\ApplicantResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ApplicantController extends Controller
{
   public function __construct()
{
    // نقوم بتفويض الصلاحيات لجميع الدوال "ما عدا" الـ store لأنها عامة للزوار
    $this->authorizeResource(Applicant::class, 'applicant', [
        'except' => ['store']
    ]);
}

  public function index(Request $request): AnonymousResourceCollection
{
    $query = Applicant::query();

    // 1. تطبيق البحث العام (بالاسم، الرقم الوطني، أو رقم المتقدم)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('FirstName', 'like', "%{$search}%")
              ->orWhere('LastName', 'like', "%{$search}%")
              ->orWhere('ApplicantNo', $search)
              ->orWhere('NationalID', $search);
        });
    }

    // 2. الفلترة حسب المدينة
    if ($request->filled('city_id')) {
        $query->where('city_id', $request->city_id);
    }

    // 3. الفلترة حسب مصدر التقديم (داخلي/خارجي)
    if ($request->filled('source')) {
        $query->where('ApplicationSource', $request->source);
    }

    // 4. الفلترة حسب حالة الحساب
    if ($request->filled('is_active')) {
        $query->where('IsActive', $request->is_active);
    }

    // تطبيق الـ Blind Scope للمدراء
    if (auth()->user()->hasRole('Department Manager')) {
        $query->blind();
    }

    $applicants = $query->with('city')->latest()->paginate(15);
    return ApplicantResource::collection($applicants);
}

  /**
     * الخطوة الأولى: إنشاء متقدم جديد أو تحديث بياناته (بوابة التوظيف الخارجية)
     */
    public function store(\App\Http\Requests\StoreApplicantRequest $request)
    {
        // 1. استخراج البيانات التي اجتازت الفحص (Validation)
        $data = $request->validated();

        // 2. البحث عن المتقدم باستخدام الرقم الوطني
        $applicant = \App\Models\Applicant::where('NationalID', $data['NationalID'])->first();

        if ($applicant) {
            // --- حالة (أ): المتقدم موجود مسبقاً ---

            // تحديث بياناته
            $applicant->update($data);

            // إذا لم يكن لديه كود تتبع (متقدم قديم)، نولد له واحداً
            if (!$applicant->resume_token) {
                $resumeToken = 'APP-' . strtoupper(\Illuminate\Support\Str::random(6));
                $applicant->update(['resume_token' => $resumeToken]);
            }
        } else {
            // --- حالة (ب): متقدم جديد تماماً ---

            // توليد كود التتبع السري (مثال: APP-X7B9)
            $data['resume_token'] = 'APP-' . strtoupper(\Illuminate\Support\Str::random(6));

            // توليد رقم المتقدم (ApplicantNo) تلقائياً (أعلى رقم + 1)
            $maxApplicantNo = \App\Models\Applicant::max('ApplicantNo');
            $data['ApplicantNo'] = $maxApplicantNo ? $maxApplicantNo + 1 : 1;

            // إنشاء السجل في قاعدة البيانات
            $applicant = \App\Models\Applicant::create($data);
        }

        // 3. إرجاع الاستجابة للواجهة الأمامية (Vue)
        return response()->json([
            'message' => 'تم حفظ البيانات المبدئية بنجاح',
            'data' => $applicant,
            'tracking_code' => $applicant->resume_token // إرسال الكود ليتم عرضه في النافذة المنبثقة
        ], 200);
    }
    /**
     * 2. دالة استكمال الطلب: التحقق من الرقم الوطني والكود معاً
     */
    public function resumeApplication(Request $request)
    {
        $request->validate([
            'NationalID' => 'required|numeric',
            'tracking_code' => 'required|string'
        ]);

        // نبحث عن التطابق الدقيق بين الرقم الوطني والكود
        $applicant = \App\Models\Applicant::where('NationalID', $request->NationalID)
                                          ->where('resume_token', $request->tracking_code)
                                          ->first();

        if (!$applicant) {
            return response()->json([
                'message' => 'بيانات الدخول غير صحيحة، تأكد من الرقم الوطني وكود التتبع.'
            ], 404);
        }

        // جلب المرفقات لمعرفة أي خطوة وصل إليها المتقدم
        $applicant->load('documents');

        return response()->json([
            'message' => 'تم استرجاع بيانات الطلب بنجاح',
            'data' => $applicant
        ], 200);
    }

    /**
     * عرض تفاصيل ملف متقدم معين
     */
    public function show(Applicant $applicant): ApplicantResource
    {
        // تحميل العلاقات ليعرضها الـ Resource
        $applicant->load(['city', 'qualifications', 'experiences', 'skills', 'documents']);
        return new ApplicantResource($applicant);
    }

    /**
     * تحديث بيانات المتقدم
     */
    public function update(UpdateApplicantRequest $request, Applicant $applicant): ApplicantResource
    {
        $applicant->update($request->validated());
        return new ApplicantResource($applicant);
    }

    /**
     * حذف مرن للمتقدم
     */
    public function destroy(Applicant $applicant)
    {
        $applicant->delete();
        return response()->json(['message' => 'تم نقل الملف إلى سلة المحذوفات بنجاح'], 200);
    }
}
