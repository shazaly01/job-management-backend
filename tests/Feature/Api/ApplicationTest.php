<?php

namespace Tests\Feature\Api;

use App\Models\Application;
use App\Models\Applicant;
use App\Models\JobRequest;
use Tests\ApiTestCase;
use Laravel\Sanctum\Sanctum;

class ApplicationTest extends ApiTestCase
{
    /**
     * اختبار عرض كافة حركات التقديم.
     */
    public function test_super_admin_can_list_all_applications(): void
    {
        Application::factory()->count(3)->create();

        $response = $this->getJson('/api/applications');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data')
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'id',
                             'TransactionNo',
                             'ApplicationStatus' // استخدمنا الاسم الحقيقي للحقل في قاعدة البيانات
                         ]
                     ]
                 ]);
    }

    /**
     * اختبار موظف التوظيف يمكنه إنشاء حركة تقديم برقم حركة طويل (TransactionNo).
     */
    public function test_recruitment_officer_can_create_application_with_long_transaction_no(): void
    {
        // تسجيل الدخول كموظف توظيف
        Sanctum::actingAs($this->recruitmentOfficer);

        $applicant = Applicant::factory()->create();
        $jobRequest = JobRequest::factory()->create();

        $payload = [
            'applicant_id' => $applicant->id,
            'job_request_id' => $jobRequest->id,
        ];

        $response = $this->postJson('/api/applications', $payload);

        $response->assertStatus(201);

        $application = Application::first();

        // التحقق من توليد رقم الحركة الطويل
        $this->assertNotNull($application->TransactionNo);
        $this->assertGreaterThan(999999999999, (int) $application->TransactionNo); // رقم ضخم جداً يعتمد على الوقت

        // الحالة الافتراضية يجب أن تكون Pending
        $this->assertEquals('Pending', $application->ApplicationStatus);
    }

    /**
     * اختبار مدير الإدارة يمكنه تحديث حالة الطلب (مثلاً من Pending إلى Shortlisted).
     */
    public function test_department_manager_can_update_application_status(): void
    {
        Sanctum::actingAs($this->departmentManager);

        $application = Application::factory()->create([
            'ApplicationStatus' => 'Pending'
        ]);

        $response = $this->putJson("/api/applications/{$application->id}", [
            'ApplicationStatus' => 'Shortlisted'
        ]);

        $response->assertStatus(200);
        $this->assertEquals('Shortlisted', $application->fresh()->ApplicationStatus);
    }

    /**
     * اختبار الأمان: منع مدير الإدارة من حذف حركة تقديم.
     */
    public function test_department_manager_cannot_delete_application(): void
    {
        Sanctum::actingAs($this->departmentManager);

        $application = Application::factory()->create();

        $response = $this->deleteJson("/api/applications/{$application->id}");

        // نتوقع 403 لأن مدير الإدارة يملك صلاحية Update و View فقط لحركات التقديم
        $response->assertStatus(403);
    }

    /**
     * اختبار موظف التوظيف يمكنه حذف حركة تقديم (Soft Delete).
     */
    public function test_recruitment_officer_can_soft_delete_application(): void
    {
        Sanctum::actingAs($this->recruitmentOfficer);

        $application = Application::factory()->create();

        $response = $this->deleteJson("/api/applications/{$application->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('applications', ['id' => $application->id]);
    }

    /**
     * اختبار المسار المخصص: جلب حركات تقديم لمتقدم معين.
     */
    public function test_can_fetch_applications_by_applicant_id(): void
    {
        $applicant = Applicant::factory()->create();

        // إنشاء حركتين لهذا المتقدم
        Application::factory()->count(2)->create(['applicant_id' => $applicant->id]);

        // إنشاء حركة لمتقدم آخر للتأكد من الفلترة
        Application::factory()->create();

        $response = $this->getJson("/api/applications/by-applicant/{$applicant->id}");

        $response->assertStatus(200)
                 ->assertJsonCount(2, 'data');
    }

    /**
     * اختبار المسار المخصص: جلب المتقدمين لشاغر وظيفي معين.
     */
    public function test_can_fetch_applications_by_job_request_id(): void
    {
        $jobRequest = JobRequest::factory()->create();

        // إنشاء 3 متقدمين لهذا الشاغر
        Application::factory()->count(3)->create(['job_request_id' => $jobRequest->id]);

        $response = $this->getJson("/api/applications/by-request/{$jobRequest->id}");

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }
}
