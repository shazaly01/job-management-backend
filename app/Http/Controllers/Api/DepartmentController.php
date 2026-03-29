<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Http\Resources\Api\DepartmentResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;

class DepartmentController extends Controller
{
    /**
     * تأمين المتحكم وتفعيل الربط التلقائي مع الـ Policy.
     * سيقوم Laravel بالتأكد من صلاحيات (viewAny, create, update, delete)
     * بناءً على ما حددناه في DepartmentPolicy.
     */
    public function __construct()
    {
        $this->authorizeResource(Department::class, 'department');
    }

    /**
     * عرض قائمة بجميع الإدارات المسجلة.
     * مفيدة لعرض الهيكل الإداري أو لاختيار إدارة عند فتح طلب توظيف جديد.
     */
    public function index(): AnonymousResourceCollection
    {
        $departments = Department::orderBy('DeptCode', 'asc')->paginate(15);
        return DepartmentResource::collection($departments);
    }

    /**
     * إنشاء إدارة جديدة في النظام.
     * يتم التحقق من فرادة DeptCode وعدم تكراره حتى مع المحذوفات (Soft Deletes).
     */
  public function store(StoreDepartmentRequest $request): DepartmentResource
{
    // 1. جلب البيانات التي اجتازت التحقق
    $data = $request->validated();

    // 2. توليد الكود وإضافته للمصفوفة $data
    $data['DeptCode'] = now()->format('ymdHis') . rand(100, 999);

    // 3. ⚠️ التعديل هنا: استخدم $data بدلاً من $request->validated()
    $department = Department::create($data);

    return new DepartmentResource($department);
}

    /**
     * عرض بيانات إدارة محددة بالتفصيل.
     */
    public function show(Department $department): DepartmentResource
    {
        return new DepartmentResource($department);
    }

    /**
     * تحديث بيانات إدارة (تغيير المسمى، الكود، أو الحالة).
     */
    public function update(UpdateDepartmentRequest $request, Department $department): DepartmentResource
    {
        $department->update($request->validated());

        return new DepartmentResource($department);
    }

    /**
     * حذف إدارة (Soft Delete).
     * ستبقى البيانات في قاعدة البيانات ولكنها لن تظهر في القوائم النشطة.
     */
    public function destroy(Department $department): JsonResponse
    {
        $department->delete();

        return response()->json([
            'status' => true,
            'message' => 'تم حذف الإدارة بنجاح ونقلها إلى الأرشيف'
        ], 200);
    }

    /**
     * دالة إضافية: جلب الإدارات النشطة فقط.
     * تُستخدم لملء القوائم المنسدلة (Dropdowns) في نماذج طلبات التوظيف.
     */
    public function getActiveDepartments(): AnonymousResourceCollection
    {
        $departments = Department::where('IsActive', true)
            ->orderBy('Name', 'asc')
            ->get();

        return DepartmentResource::collection($departments);
    }
}
