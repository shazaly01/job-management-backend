<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إعادة تعيين الأدوار والصلاحيات المخزنة مؤقتاً (cache)
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // --- تعريف الحارس ---
        $guardName = 'api';

        // --- قائمة الصلاحيات الجديدة لمشروع نظام التوظيف ---
        $permissions = [
            'dashboard.view',

            // إدارة النظام الأساسية
            'user.view', 'user.create', 'user.update', 'user.delete',
            'role.view', 'role.create', 'role.update', 'role.delete',
            'setting.view', 'setting.update',
            'backup.view', 'backup.create', 'backup.delete', 'backup.download',

            // الكيانات القاموسية
            'city.view', 'city.create', 'city.update', 'city.delete',
            'department.view', 'department.create', 'department.update', 'department.delete',

            // كيانات التوظيف الأساسية
            'applicant.view', 'applicant.create', 'applicant.update', 'applicant.delete',
            'job_request.view', 'job_request.create', 'job_request.update', 'job_request.delete',
            'application.view', 'application.create', 'application.update', 'application.delete',
            'interview.view', 'interview.create', 'interview.update', 'interview.delete',
            'document.view', 'document.create', 'document.update', 'document.delete',
        ];

        // إنشاء الصلاحيات مع تحديد الحارس
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => $guardName,
            ]);
        }

        // --- إنشاء الأدوار الجديدة وتوزيع الصلاحيات ---

        // 1. دور "Super Admin" (مدير النظام)
        // هذا الدور يحصل على كل الصلاحيات تلقائيًا عبر AuthServiceProvider (Gate::before)
        Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => $guardName,
        ]);

        // 2. دور "Recruitment Officer" (موظف مكتب التوظيف)
        $recruitmentOfficer = Role::firstOrCreate([
            'name' => 'Recruitment Officer',
            'guard_name' => $guardName,
        ]);

        // موظف التوظيف يدير المتقدمين، الملفات، الطلبات، المقابلات، ويرى الإدارات والمدن
        $officerPermissions = [
            'dashboard.view',
            'applicant.view', 'applicant.create', 'applicant.update', 'applicant.delete',
            'application.view', 'application.create', 'application.update', 'application.delete',
            'interview.view', 'interview.create', 'interview.update', 'interview.delete',
            'document.view', 'document.create', 'document.update', 'document.delete',
            'job_request.view', // ليرى الاحتياجات فقط دون تعديلها
            'city.view', 'department.view'
        ];
        $recruitmentOfficer->syncPermissions($officerPermissions);


        // 3. دور "Department Manager" (مدير إدارة)
        $departmentManager = Role::firstOrCreate([
            'name' => 'Department Manager',
            'guard_name' => $guardName,
        ]);

        // مدير الإدارة يطلب كوادر، يرى المتقدمين (بشكل مجهول)، ويقيم المقابلات
        $managerPermissions = [
            'dashboard.view',
            'job_request.view', 'job_request.create', 'job_request.update', 'job_request.delete',
            'applicant.view', // الصلاحية هنا للعرض فقط، وحجب الأسماء يتم في الـ Controller
            'application.view', 'application.update', // لتغيير حالة الطلب (قبول/رفض)
            'interview.view', 'interview.update', // لتقييم المقابلة
            'document.view' // ليرى السيرة الذاتية إذا سُمح له في مرحلة معينة
        ];
        $departmentManager->syncPermissions($managerPermissions);
    }
}
