<?php

namespace Tests\Feature\Api;

use App\Models\JobRequest;
use App\Models\Department;
use Tests\ApiTestCase;
use Laravel\Sanctum\Sanctum;

class JobRequestTest extends ApiTestCase
{
    /**
     * اختبار عرض قائمة طلبات التوظيف لمدير النظام.
     */
    public function test_super_admin_can_list_job_requests(): void
    {
        JobRequest::factory()->count(3)->create();

        $response = $this->getJson('/api/job-requests');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data')
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'RequestNo', 'Department', 'Status']
                     ]
                 ]);
    }

    /**
     * اختبار مدير الإدارة يمكنه إنشاء طلب توظيف والتحقق من الرقم الطويل.
     */
    public function test_department_manager_can_create_job_request_with_long_number(): void
    {
        // تسجيل الدخول كمدير إدارة
        Sanctum::actingAs($this->departmentManager);

        $department = Department::factory()->create();

        $payload = [
            'department_id' => $department->id,
            'RequiredDegreeLevel' => 'Bachelor',
            'RequiredMajor' => 'Computer Science',
            'RequiredYearsOfExperience' => 3,
            'Status' => 'Open'
        ];

        $response = $this->postJson('/api/job-requests', $payload);

        $response->assertStatus(201);

        // التحقق من قاعدة البيانات
        $jobRequest = JobRequest::first();

        // التحقق من أن رقم الطلب تم توليده تلقائياً وهو رقم طويل
        $this->assertNotNull($jobRequest->RequestNo);
        $this->assertGreaterThan(999999999, (int) $jobRequest->RequestNo);

        // التحقق من الاستجابة
        $response->assertJsonPath('data.RequiredMajor', 'Computer Science');
    }

    /**
     * اختبار موظف التوظيف لا يمكنه إنشاء طلب توظيف (اختبار الأمان).
     */
    public function test_recruitment_officer_cannot_create_job_request(): void
    {
        // تسجيل الدخول كموظف توظيف
        Sanctum::actingAs($this->recruitmentOfficer);

        $department = Department::factory()->create();

        $payload = [
            'department_id' => $department->id,
            'RequiredDegreeLevel' => 'Master',
            'RequiredMajor' => 'Accounting',
            'RequiredYearsOfExperience' => 5,
            'Status' => 'Open'
        ];

        $response = $this->postJson('/api/job-requests', $payload);

        // نتوقع 403 لأن موظف التوظيف يملك صلاحية view فقط وليس create
        $response->assertStatus(403);
    }

    /**
     * اختبار عرض تفاصيل طلب توظيف واحد.
     */
    public function test_can_show_job_request_details(): void
    {
        $jobRequest = JobRequest::factory()->create();

        $response = $this->getJson("/api/job-requests/{$jobRequest->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => ['id', 'RequestNo', 'RequiredMajor']
                 ]);
    }

    /**
     * اختبار تحديث طلب توظيف من قبل مدير الإدارة.
     */
    public function test_department_manager_can_update_job_request(): void
    {
        Sanctum::actingAs($this->departmentManager);

        $jobRequest = JobRequest::factory()->create([
            'Status' => 'Open'
        ]);

        $response = $this->putJson("/api/job-requests/{$jobRequest->id}", [
            'department_id' => $jobRequest->department_id,
            'RequiredDegreeLevel' => $jobRequest->RequiredDegreeLevel,
            'RequiredMajor' => 'Updated Major',
            'RequiredYearsOfExperience' => 10,
            'Status' => 'Closed' // تغيير الحالة
        ]);

        $response->assertStatus(200);
        $this->assertEquals('Closed', $jobRequest->fresh()->Status);
        $this->assertEquals('Updated Major', $jobRequest->fresh()->RequiredMajor);
    }

    /**
     * اختبار الحذف المرن لطلب التوظيف.
     */
    public function test_department_manager_can_soft_delete_job_request(): void
    {
        Sanctum::actingAs($this->departmentManager);

        $jobRequest = JobRequest::factory()->create();

        $response = $this->deleteJson("/api/job-requests/{$jobRequest->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('job_requests', ['id' => $jobRequest->id]);
    }

    /**
     * اختبار جلب الطلبات المفتوحة فقط (المسار المخصص).
     */
    public function test_can_fetch_open_requests_only(): void
    {
        // إنشاء طلبين (مفتوح ومغلق)
        JobRequest::factory()->create(['Status' => 'Open']);
        JobRequest::factory()->create(['Status' => 'Closed']);

        $response = $this->getJson('/api/job-requests/open');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data'); // يجب أن يرجع طلب واحد فقط

        $response->assertJsonPath('data.0.Status', 'Open');
    }
}
