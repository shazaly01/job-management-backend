<?php

namespace Tests\Feature\Api;

use App\Models\Interview;
use App\Models\Application;
use Tests\ApiTestCase;
use Laravel\Sanctum\Sanctum;

class InterviewTest extends ApiTestCase
{
    /**
     * اختبار عرض كافة المقابلات.
     */
    public function test_super_admin_can_list_interviews(): void
    {
        Interview::factory()->count(3)->create();

        $response = $this->getJson('/api/interviews');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data')
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'EmpCode', 'InterviewDate', 'Result']
                     ]
                 ]);
    }

    /**
     * اختبار جدولة مقابلة جديدة مع التأكد من طول كود الموظف (EmpCode).
     */
    public function test_can_schedule_interview_with_long_emp_code(): void
    {
        Sanctum::actingAs($this->recruitmentOfficer);

        $application = Application::factory()->create();

        $payload = [
            'application_id' => $application->id,
            'EmpCode' => '987654321012345', // كود موظف طويل جداً (15 رقم)
            'InterviewDate' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'Result' => 'Pending' // لم يتم التقييم بعد
        ];

        $response = $this->postJson('/api/interviews', $payload);

        $response->assertStatus(201);

        $interview = Interview::first();

        // التحقق من أن رقم الموظف تم حفظه بدقة وأنه أكبر من 9 خانات
        $this->assertGreaterThan(999999999, (int) $interview->EmpCode);
        $this->assertEquals('987654321012345', $interview->EmpCode);
    }

    /**
     * اختبار مدير الإدارة يمكنه تقييم المقابلة (تحديث النتيجة والدرجة).
     */
    public function test_department_manager_can_evaluate_interview(): void
    {
        Sanctum::actingAs($this->departmentManager);

        $interview = Interview::factory()->create([
            'EvaluationScore' => null,
            'Result' => 'Pending'
        ]);

        $response = $this->putJson("/api/interviews/{$interview->id}", [
            'application_id' => $interview->application_id,
            'EmpCode' => $interview->EmpCode,
            'InterviewDate' => $interview->InterviewDate,
            'EvaluationScore' => 85.50, // رصد الدرجة
            'Notes' => 'ممتاز في الجانب التقني',
            'Result' => 'Passed' // تغيير النتيجة إلى ناجح
        ]);

        $response->assertStatus(200);

        $interview->refresh();
        $this->assertEquals('Passed', $interview->Result);
        $this->assertEquals(85.50, $interview->EvaluationScore);
    }

    /**
     * اختبار عرض تفاصيل مقابلة معينة.
     */
    public function test_can_show_interview_details(): void
    {
        $interview = Interview::factory()->create();

        $response = $this->getJson("/api/interviews/{$interview->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => ['id', 'EmpCode', 'EvaluationScore']
                 ]);
    }

    /**
     * اختبار المسار المخصص: جلب المقابلات لموظف معين (أجندة الموظف).
     */
    public function test_can_fetch_interviews_by_interviewer_emp_code(): void
    {
        $targetEmpCode = '111222333444';

        // إنشاء مقابلتين لهذا الموظف
        Interview::factory()->count(2)->create(['EmpCode' => $targetEmpCode]);

        // إنشاء مقابلة لموظف آخر للتأكد من صحة الفلترة
        Interview::factory()->create(['EmpCode' => '999888777666']);

        $response = $this->getJson("/api/interviews/by-interviewer/{$targetEmpCode}");

        $response->assertStatus(200)
                 ->assertJsonCount(2, 'data'); // يجب أن ترجع مقابلتين فقط
    }

    /**
     * اختبار الحذف المرن لسجل المقابلة.
     */
    public function test_can_soft_delete_interview(): void
    {
        Sanctum::actingAs($this->recruitmentOfficer);

        $interview = Interview::factory()->create();

        $response = $this->deleteJson("/api/interviews/{$interview->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('interviews', ['id' => $interview->id]);
    }
}
