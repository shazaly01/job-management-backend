<?php

namespace Tests\Feature\Api;

use App\Models\Applicant;
use App\Models\City;
use Tests\ApiTestCase;
use Laravel\Sanctum\Sanctum;

class ApplicantTest extends ApiTestCase
{
    /**
     * اختبار عرض قائمة المتقدمين للمسؤول (Super Admin).
     */
    public function test_super_admin_can_list_applicants(): void
    {
        Applicant::factory()->count(5)->create();

        $response = $this->getJson('/api/applicants');

        $response->assertStatus(200)
                 ->assertJsonCount(5, 'data')
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'ApplicantNo', 'FirstName', 'LastName', 'City']
                     ]
                 ]);
    }

    /**
     * اختبار إنشاء متقدم جديد والتحقق من توليد الأرقام الطويلة.
     */
    public function test_can_create_applicant_with_auto_generated_long_numbers(): void
    {
        $city = City::factory()->create();

        $payload = [
            'NationalID' => '123456789012345', // 15 رقم
            'FirstName'  => 'Ahmed',
            'LastName'   => 'Ali',
            'Email'      => 'ahmed@example.com',
            'PhoneNumber'=> '0910000000',
            'city_id'    => $city->id,
            'ApplicationSource' => 'Online'
        ];

        $response = $this->postJson('/api/applicants', $payload);

        $response->assertStatus(201);

        // التحقق من أن الرقم المولد طويل (أكثر من 9 أرقام) كما اشترطت لـ DECIMAL(18,0)
        $applicant = Applicant::first();
        $this->assertGreaterThan(999999999, (int) $applicant->ApplicantNo);
        $this->assertNotNull($applicant->ReferenceCode);

        $response->assertJsonPath('data.FirstName', 'Ahmed');
    }

    /**
     * اختبار عرض تفاصيل متقدم واحد مع علاقاته.
     */
    public function test_can_show_applicant_details(): void
    {
        $applicant = Applicant::factory()->create();

        $response = $this->getJson("/api/applicants/{$applicant->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => ['id', 'ApplicantNo', 'FirstName', 'LastName']
                 ]);
    }

    /**
     * اختبار تحديث بيانات متقدم.
     */
    public function test_can_update_applicant_info(): void
    {
        $applicant = Applicant::factory()->create(['FirstName' => 'OldName']);

        $response = $this->putJson("/api/applicants/{$applicant->id}", [
            'FirstName' => 'NewName',
            'LastName'  => $applicant->LastName,
            'NationalID' => $applicant->NationalID,
            'city_id'    => $applicant->city_id,
            'ApplicationSource' => $applicant->ApplicationSource
        ]);

        $response->assertStatus(200);
        $this->assertEquals('NewName', $applicant->fresh()->FirstName);
    }

    /**
     * اختبار الحذف المرن للمتقدم.
     */
    public function test_can_soft_delete_applicant(): void
    {
        $applicant = Applicant::factory()->create();

        $response = $this->deleteJson("/api/applicants/{$applicant->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('applicants', ['id' => $applicant->id]);
    }

    /**
     * اختبار الصلاحيات: هل يمنع مدير الإدارة من الحذف؟
     * تم استبدال Auditor بـ DepartmentManager ليتوافق مع ApiTestCase المحدث
     */
    public function test_department_manager_cannot_delete_applicant(): void
    {
        // تسجيل الدخول كمدير إدارة (لا يملك صلاحية حذف المتقدمين)
        Sanctum::actingAs($this->departmentManager);

        $applicant = Applicant::factory()->create();

        $response = $this->deleteJson("/api/applicants/{$applicant->id}");

        // نتوقع رفض العملية (403 Forbidden) لأن الدور لا يمتلك صلاحية 'applicant.delete'
        $response->assertStatus(403);
    }

    /**
     * اختبار التحقق من صحة البيانات (Validation).
     */
    public function test_cannot_create_applicant_with_duplicate_national_id(): void
    {
        Applicant::factory()->create(['NationalID' => '111222333444']);

        $payload = [
            'NationalID' => '111222333444', // مكرر
            'FirstName'  => 'Test',
            'LastName'   => 'User',
            'city_id'    => City::factory()->create()->id,
            'ApplicationSource' => 'Online'
        ];

        $response = $this->postJson('/api/applicants', $payload);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['NationalID']);
    }
}
