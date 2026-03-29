<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use App\Models\Owner;
use Illuminate\Http\Request;
use App\Models\Project;

class ReportController extends Controller
{
    /**
     * تقرير عام: ملخص مالي لكل الشركات
     * يعرض: عدد المشاريع، إجمالي القيم المستحقة، المدفوع، والمتبقي لكل شركة
     */
    public function companiesSummary(): JsonResponse
    {
        // --- [التعديل هنا] ---
        // تم تغيير 'contract_value' إلى 'due_value' في الاستعلام
       $companies = Company::withCount('projects')
    ->withSum('projects as total_due_value', 'due_value')
    ->withSum('projects as total_contract_value', 'contract_value') // <--- إضافة هذا السطر
    ->get();


        $data = $companies->map(function ($company) {
            // حساب إجمالي المدفوعات لكل مشاريع هذه الشركة
            $totalPaid = $company->projects()->withSum('payments', 'amount')->get()->sum('payments_sum_amount');

            // --- [التعديل هنا] ---
            // تم تغيير 'total_contracts_value' إلى 'total_due_value'
            $dueValue = (float) ($company->total_due_value ?? 0); // <--- تم التغيير
            $paidValue = (float) ($totalPaid ?? 0);

            return [
                'id' => $company->id,
                'name' => $company->name,
                'license_number' => $company->license_number,
                'tax_number' => $company->tax_number,
                'projects_count' => $company->projects_count,
                'total_contract_value' => (float) ($company->total_contract_value ?? 0),
                // --- [التعديل هنا] ---
                // تم تغيير اسم المفتاح في الـ JSON الناتج
                'total_due_value' => $dueValue, // <--- تم التغيير
                'total_paid' => $paidValue,
                'total_remaining' => $dueValue - $paidValue,
            ];
        });

        return response()->json([
            'data' => $data,
            'grand_summary' => [
                'total_companies' => $data->count(),
                'total_projects' => $data->sum('projects_count'),
                'grand_total_contract_value' => $data->sum('total_contract_value'),
                // --- [التعديل هنا] ---
                // تم تغيير أسماء المفاتيح في الإجماليات
                'grand_total_due_value' => $data->sum('total_due_value'), // <--- تم التغيير
                'grand_total_paid' => $data->sum('total_paid'),
                'grand_total_remaining' => $data->sum('total_remaining'),
            ]
        ]);
    }

    /**
     * كشف حساب لشركة معينة
     */
    public function companyStatement(Company $company): JsonResponse
    {
        // تحميل المشاريع مع حساب إجمالي الدفعات لكل مشروع
        $projects = $company->projects()->withSum('payments', 'amount')->get();

        $projectsData = $projects->map(function ($project) {
            $totalPaid = $project->payments_sum_amount ?? 0;
            return [
                'id' => $project->id,
                'name' => $project->name,
                'contract_value' => (float) $project->contract_value,
                'contract_number' => $project->contract_number,
                'project_owner' => $project->project_owner,
                'region' => $project->region, // أضفت المنطقة أيضاً للاحتياط
                // --- [التعديل هنا] ---
                // تم تغيير 'contract_value' إلى 'due_value'
                'due_value' => (float) $project->due_value, // <--- تم التغيير
                'total_paid' => (float) $totalPaid,
                'remaining' => (float) $project->due_value - $totalPaid, // <--- تم التغيير
                'disbursement_status' => $project->disbursement_status,
                'contractual_status' => $project->contractual_status,
            ];
        });

        // --- [التعديل هنا] ---
        // حساب الإجماليات باستخدام الاسم الجديد
        $totalDueValue = $projectsData->sum('due_value'); // <--- تم التغيير
        $totalPaymentsReceived = $projectsData->sum('total_paid');

        return response()->json([
            'data' => [
                'company' => [
                    'id' => $company->id,
                    'name' => $company->name,
                    'tax_number' => $company->tax_number,
                    'license_number' => $company->license_number,
                    'owner_name' => $company->owner_name,
                    'address' => $company->address,
                ],
                'projects' => $projectsData,
                'summary' => [
                    // --- [التعديل هنا] ---
                    // تم تغيير أسماء المفاتيح في ملخص كشف الحساب
                    'total_due_value' => $totalDueValue, // <--- تم التغيير
                    'total_contract_value' => $projectsData->sum('contract_value'),
                    'total_payments_received' => $totalPaymentsReceived,
                    'total_remaining' => $totalDueValue - $totalPaymentsReceived,
                ]
            ]
        ]);
    }



    /**
     * +++ [جديد] كشف حساب لجهة مالكة معينة +++
     */
    public function ownerStatement(Owner $owner): JsonResponse
    {
        // 1. نجلب كل المشاريع المرتبطة بهذا المالك
        // نحتاج أيضاً معرفة "الشركة" المنفذة لكل مشروع (with company)
        $projects = $owner->projects()
            ->with('company')
            ->withSum('payments', 'amount')
            ->get();

        // 2. تنسيق بيانات المشاريع
        $projectsData = $projects->map(function ($project) {
            $totalPaid = $project->payments_sum_amount ?? 0;
            return [
                'id' => $project->id,
                'name' => $project->name,
                'contract_value' => (float) $project->contract_value,
                'contract_number' => $project->contract_number,
                'region' => $project->region,
                'due_value' => (float) $project->due_value,
                'total_paid' => (float) $totalPaid,
                'remaining' => (float) $project->due_value - $totalPaid,
                'disbursement_status' => $project->disbursement_status,
                'contractual_status' => $project->contractual_status,

                // بيانات الشركة المنفذة (مهمة في تقرير المالك)
                'company' => $project->company ? [
                    'id' => $project->company->id,
                    'name' => $project->company->name,
                ] : null,
            ];
        });

        // 3. حساب الإجماليات
        $totalDueValue = $projectsData->sum('due_value');
        $totalPaymentsReceived = $projectsData->sum('total_paid');

        // 4. إرجاع الاستجابة بنفس هيكلية كشف حساب الشركة لسهولة التعامل في الفرونت
        return response()->json([
            'data' => [
                'owner' => [ // بدلاً من company، نعيد owner
                    'id' => $owner->id,
                    'name' => $owner->name,
                    // أي بيانات أخرى للمالك إذا وجدت
                ],
                'projects' => $projectsData,
                'summary' => [
                    'total_due_value' => $totalDueValue,
                    'total_contract_value' => $projectsData->sum('contract_value'),
                    'total_payments_received' => $totalPaymentsReceived,
                    'total_remaining' => $totalDueValue - $totalPaymentsReceived,
                ]
            ]
        ]);
    }




        /**
     * +++ [جديد] تقرير مالي للمشاريع بناءً على الفلاتر +++
     */
    public function projectsReportByFilter(Request $request): JsonResponse
    {
        // 1. التحقق من صحة الفلاتر (اختياري لكن موصى به)
        $request->validate([
            'project_type_id' => 'nullable|integer|exists:project_types,id',
            'completion_status' => 'nullable|string|in:completed,in_progress,not_started,almost_done,just_started',
        ]);

        // 2. بناء الاستعلام الأساسي
        $query = Project::query()
            ->with(['company', 'owner']) // تحميل العلاقات لعرضها في التقرير
            ->withSum('payments', 'amount'); // حساب مجموع الدفعات لكل مشروع

        // 3. تطبيق الفلاتر الديناميكية
        if ($projectTypeId = $request->input('project_type_id')) {
            $query->where('project_type_id', $projectTypeId);
        }

        if ($completionStatus = $request->input('completion_status')) {
            switch ($completionStatus) {
                case 'completed':
                    $query->where('completion_percentage', 100);
                    break;
                case 'not_started':
                    $query->where('completion_percentage', 0);
                    break;
                case 'in_progress':
                    $query->whereBetween('completion_percentage', [1, 99]);
                    break;
                case 'almost_done':
                    $query->where('completion_percentage', '>=', 80)
                          ->where('completion_percentage', '<', 100);
                    break;
                case 'just_started':
                    $query->where('completion_percentage', '<=', 20)
                          ->where('completion_percentage', '>', 0);
                    break;
            }
        }

        // 4. تنفيذ الاستعلام
        $projects = $query->get();

        // 5. تنسيق بيانات المشاريع (نفس منطق الدوال الأخرى)
        $projectsData = $projects->map(function ($project) {
            $totalPaid = $project->payments_sum_amount ?? 0;
            return [
                'id' => $project->id,
                'name' => $project->name,
                'contract_value' => (float) $project->contract_value,
                'due_value' => (float) $project->due_value,
                'total_paid' => (float) $totalPaid,
                'remaining' => (float) $project->due_value - $totalPaid,
                'disbursement_status' => $project->disbursement_status,
                'contractual_status' => $project->contractual_status,
                'company' => $project->company ? ['id' => $project->company->id, 'name' => $project->company->name] : null,
                'owner' => $project->owner ? ['id' => $project->owner->id, 'name' => $project->owner->name] : null,
            ];
        });

        // 6. حساب الإجماليات المالية
        $totalDueValue = $projectsData->sum('due_value');
        $totalPaymentsReceived = $projectsData->sum('total_paid');

        // 7. إرجاع الاستجابة
        return response()->json([
            'data' => [
                // لا يوجد 'company' أو 'owner' هنا، بل نعيد معلومات عن الفلاتر المستخدمة
                'report_info' => [
                    'title' => 'تقرير مشاريع مخصص',
                    'filters' => $request->only(['project_type_id', 'completion_status'])
                ],
                'projects' => $projectsData,
                'summary' => [
                    'total_due_value' => $totalDueValue,
                    'total_contract_value' => $projectsData->sum('contract_value'),
                    'total_payments_received' => $totalPaymentsReceived,
                    'total_remaining' => $totalDueValue - $totalPaymentsReceived,
                ]
            ]
        ]);
    }
}
