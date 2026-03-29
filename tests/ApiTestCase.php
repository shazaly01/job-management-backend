<?php

namespace Tests;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

abstract class ApiTestCase extends TestCase
{
    use RefreshDatabase;

    // تعريف المستخدمين بناءً على أدوار نظام التوظيف الجديد
    protected User $superAdmin;
    protected User $recruitmentOfficer; // بديل لـ Admin
    protected User $departmentManager;  // بديل لـ Data Entry

    /**
     * الإعداد قبل كل اختبار
     */
    protected function setUp(): void
    {
        parent::setUp();

        // 1. تشغيل الـ Seeder الجديد الذي أرسلته (يحتوي على api guard)
        $this->seed(PermissionSeeder::class);

        // 2. إنشاء المستخدمين وتعيين الأدوار الصحيحة من الـ Seeder

        // مدير النظام
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole('Super Admin');

        // موظف التوظيف
        $this->recruitmentOfficer = User::factory()->create();
        $this->recruitmentOfficer->assignRole('Recruitment Officer');

        // مدير الإدارة
        $this->departmentManager = User::factory()->create();
        $this->departmentManager->assignRole('Department Manager');

        // 3. تسجيل الدخول افتراضيًا كـ Super Admin لبدء الاختبارات
        Sanctum::actingAs($this->superAdmin);
    }
}
