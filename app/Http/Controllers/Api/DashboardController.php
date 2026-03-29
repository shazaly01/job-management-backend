<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\Application;
use App\Models\Department;
use App\Models\Interview;
use App\Models\JobRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * جلب إحصائيات لوحة التحكم الرئيسية
     */
    public function index(Request $request): JsonResponse
    {
        // 1. إحصائيات الأرقام السريعة (Quick Stats)
        $stats = [
            'total_departments' => Department::where('IsActive', true)->count(),
            'open_job_requests' => JobRequest::whereIn('Status', ['مفتوح', 'Open'])->count(),
            'total_applicants'  => Applicant::where('IsActive', true)->count(),
            'total_applications'=> Application::count(),
        ];

        // 2. إحصائيات التقديمات حسب الحالة (لرسم المخططات البيانية - Charts)
        // مثال: كم طلب "جديد"، "قيد المراجعة"، "مقبول"، "مرفوض"
        $applicationsByStatus = Application::select('ApplicationStatus', DB::raw('count(*) as count'))
            ->groupBy('ApplicationStatus')
            ->pluck('count', 'ApplicationStatus');

        // 3. المقابلات القادمة (Upcoming Interviews)
        // عرض أقرب 5 مقابلات مجدولة لم تتم بعد
        $upcomingInterviews = Interview::with(['application.applicant', 'application.jobRequest'])
            ->whereNotNull('InterviewDate')
            ->where('InterviewDate', '>=', now())
            ->orderBy('InterviewDate', 'asc')
            ->take(5)
            ->get()
            ->map(function ($interview) {
                return [
                    'id' => $interview->id,
                    // تحويل رقم الموظف لنص كما اتفقنا في القواعد السابقة لحماية دقة الرقم
                    'EmpCode' => (string) $interview->EmpCode,
                    'InterviewDate' => $interview->InterviewDate->format('Y-m-d H:i'),
                    'ApplicantName' => $interview->application->applicant->FirstName . ' ' . $interview->application->applicant->LastName,
                    'JobTitle' => $interview->application->jobRequest->RequiredMajor ?? 'غير محدد',
                ];
            });

        // 4. أحدث التقديمات (Recent Applications)
        // لعرض جدول صغير بآخر 5 أشخاص قدموا على الوظائف
        $recentApplications = Application::with(['applicant', 'jobRequest.department'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($app) {
                return [
                    'id' => $app->id,
                    'TransactionNo' => (string) $app->TransactionNo,
                    'ApplicantName' => $app->applicant ? ($app->applicant->FirstName . ' ' . $app->applicant->LastName) : 'مجهول',
                    'DepartmentName' => $app->jobRequest->department->Name ?? 'غير محدد',
                    'Status' => $app->ApplicationStatus,
                    'SubmittedAt' => $app->created_at->diffForHumans(), // مثلاً: "منذ ساعتين"
                ];
            });

        // 5. تطبيق نطاق الرؤية المجهولة (Blind Scope) إذا لزم الأمر
        // إذا كان المستخدم مدير إدارة، يمكننا تقييد البيانات لتعرض فقط ما يخص إدارته
        $user = $request->user();
        if ($user && $user->hasRole('Department Manager')) {
            // هنا يمكنك تخصيص الاستعلامات لتعود فقط ببيانات الإدارة الخاصة به
            // (تُركت كفكرة برمجية لتطبيقها حسب هيكل علاقة المستخدم بالإدارة لديك)
        }

        // إرجاع الاستجابة المجمعة
        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats,
                'applications_chart' => $applicationsByStatus,
                'upcoming_interviews' => $upcomingInterviews,
                'recent_applications' => $recentApplications,
            ]
        ]);
    }
}
