<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// --- استيراد الـ Controllers (النظام الأساسي) ---
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\BackupController;

// --- استيراد الـ Controllers (نظام التوظيف) ---
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\ApplicantController;
use App\Http\Controllers\Api\ApplicantQualificationController;
use App\Http\Controllers\Api\ApplicantExperienceController;
use App\Http\Controllers\Api\ApplicantSkillController;
use App\Http\Controllers\Api\JobRequestController;
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\InterviewController;
use App\Http\Controllers\Api\DocumentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ==========================================
// 1. المسارات العامة (Public Routes - لا تتطلب تسجيل دخول)
// ==========================================

Route::post('/login', [AuthController::class, 'login']);

/**
 * ⚠️ هام جداً: مسار إنشاء متقدم جديد يجب أن يكون عاماً
 * لكي تعمل بوابة التوظيف الخارجية للزوار
 */
Route::post('/applicants', [ApplicantController::class, 'store']);
Route::post('/documents', [DocumentController::class, 'store']); // <--- أضف هذا السطر هنا ✅
Route::post('/applicants/resume', [ApplicantController::class, 'resumeApplication']);
Route::get('job-requests/detail/{slug}', [JobRequestController::class, 'showBySlug']);
// ==========================================
// 2. المسارات المحمية (Protected Routes - تتطلب Token)
// ==========================================
Route::middleware('auth:sanctum')->group(function () {

    // --- أ. الحساب الشخصي وتسجيل الخروج ---
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return response()->json($request->user()->load('roles.permissions'));
    });

    // --- ب. الإحصائيات (Dashboard) ---
    Route::get('/dashboard/stats', [DashboardController::class, 'index']);

    // --- ج. إدارة النظام (Users, Roles, Backups) ---
    Route::apiResource('users', UserController::class);
    Route::get('roles/permissions', [RoleController::class, 'getAllPermissions']);
    Route::apiResource('roles', RoleController::class);

    Route::prefix('backups')->group(function () {
        Route::get('/', [BackupController::class, 'index']);
        Route::post('/', [BackupController::class, 'store']);
        Route::get('/download', [BackupController::class, 'download']);
        Route::delete('/', [BackupController::class, 'destroy']);
    });

    // ==========================================
    // 3. نظام التوظيف (Recruitment System)
    // ==========================================

    // المدن والإدارات
    Route::get('cities/active', [CityController::class, 'getActiveCities']);
    Route::apiResource('cities', CityController::class);

    Route::get('departments/active', [DepartmentController::class, 'getActiveDepartments']);
    Route::apiResource('departments', DepartmentController::class);

    // المتقدمين (إدارة المتقدمين للمدراء - باستثناء الإنشاء لأنه عام)
    Route::apiResource('applicants', ApplicantController::class)->except(['store']);

    // تفاصيل المتقدم (مؤهلات، خبرات، مهارات)
    Route::get('applicants/{applicant}/qualifications', [ApplicantQualificationController::class, 'getByApplicant']);
    Route::apiResource('qualifications', ApplicantQualificationController::class);

    Route::get('applicants/{applicant}/experiences', [ApplicantExperienceController::class, 'getByApplicant']);
    Route::apiResource('experiences', ApplicantExperienceController::class);

    Route::get('applicants/{applicant}/skills', [ApplicantSkillController::class, 'getByApplicant']);
    Route::apiResource('skills', ApplicantSkillController::class);

    // طلبات التوظيف (Job Requests)
    Route::get('job-requests/open', [JobRequestController::class, 'getOpenRequests']);
    Route::apiResource('job-requests', JobRequestController::class);

    // تقديمات الوظائف (Applications)
    Route::get('applications/by-applicant/{applicant}', [ApplicationController::class, 'getByApplicant']);
    Route::get('applications/by-request/{request}', [ApplicationController::class, 'getByJobRequest']);
    Route::apiResource('applications', ApplicationController::class);

    // المقابلات (Interviews)
    Route::get('interviews/by-interviewer/{empCode}', [InterviewController::class, 'getByInterviewer']);
    Route::get('interviews/by-application/{application}', [InterviewController::class, 'getByApplication']); // <--- ✅ أضف هذا السطر
    Route::apiResource('interviews', InterviewController::class);

    // المستندات
    Route::apiResource('documents', DocumentController::class)
        ->only(['index', 'show', 'destroy'])
        ->except(['store']);
});

/**
 * مسار تحميل المستندات الموقّع (خارج المصادقة لأنه يعتمد على التوقيع الأمني)
 */
Route::get('/documents/download/{document}', [DocumentController::class, 'download'])
    ->name('documents.download')
    ->middleware('signed');
