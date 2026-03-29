<?php

namespace Tests\Feature\Api;

use App\Models\Applicant;
use App\Models\ApplicantQualification;
use App\Models\ApplicantExperience;
use App\Models\ApplicantSkill;
use Tests\ApiTestCase;
use Laravel\Sanctum\Sanctum;

class ApplicantCVTest extends ApiTestCase
{
    // ==========================================
    // 1. اختبارات المؤهلات العلمية (Qualifications)
    // ==========================================

    public function test_recruitment_officer_can_add_qualification(): void
    {
        Sanctum::actingAs($this->recruitmentOfficer);
        $applicant = Applicant::factory()->create();

        $payload = [
            'applicant_id' => $applicant->id,
            'DegreeLevel' => 'Bachelor',
            'Major' => 'Computer Science',
            'GraduationYear' => 2020,
            'UniversityOrInstitute' => 'Test University',
            'GPA_or_Grade' => 'Excellent',
        ];

        // تم التعديل ليطابق: Route::apiResource('qualifications', ...)
        $response = $this->postJson('/api/qualifications', $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('applicant_qualifications', [
            'applicant_id' => $applicant->id,
            'DegreeLevel' => 'Bachelor'
        ]);
    }

   public function test_can_delete_qualification(): void
    {
        Sanctum::actingAs($this->recruitmentOfficer);

        // 1. إنشاء المتقدم أولاً للتأكد من وجوده
        $applicant = Applicant::factory()->create();

        // 2. إنشاء المؤهل العلمي وربطه صراحةً بالمتقدم الذي أنشأناه
        $qualification = ApplicantQualification::factory()->create([
            'applicant_id' => $applicant->id
        ]);

        $response = $this->deleteJson("/api/qualifications/{$qualification->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('applicant_qualifications', ['id' => $qualification->id]);
    }

    // ==========================================
    // 2. اختبارات الخبرات العملية (Experiences)
    // ==========================================

    public function test_recruitment_officer_can_add_experience(): void
    {
        Sanctum::actingAs($this->recruitmentOfficer);
        $applicant = Applicant::factory()->create();

        $payload = [
            'applicant_id' => $applicant->id,
            'JobTitle' => 'Software Engineer',
            'CompanyName' => 'Tech Cloud',
            'StartDate' => '2021-01-01',
            'EndDate' => '2023-01-01',
            'JobDescription' => 'Developed APIs using Laravel.'
        ];

        // تم التعديل ليطابق: Route::apiResource('experiences', ...)
        $response = $this->postJson('/api/experiences', $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('applicant_experiences', [
            'JobTitle' => 'Software Engineer'
        ]);
    }

    // ==========================================
    // 3. اختبارات المهارات (Skills)
    // ==========================================

    public function test_recruitment_officer_can_add_skill(): void
    {
        Sanctum::actingAs($this->recruitmentOfficer);
        $applicant = Applicant::factory()->create();

        $payload = [
            'applicant_id' => $applicant->id,
            'SkillName' => 'Laravel Framework',
            'ProficiencyLevel' => 'Advanced'
        ];

        // تم التعديل ليطابق: Route::apiResource('skills', ...)
        $response = $this->postJson('/api/skills', $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('applicant_skills', [
            'SkillName' => 'Laravel Framework'
        ]);
    }

    /**
     * اختبار الأمان: مدير الإدارة لا يمكنه التعديل على السيرة الذاتية للمتقدم
     */
    public function test_department_manager_cannot_add_skills(): void
    {
        Sanctum::actingAs($this->departmentManager); // دور مدير الإدارة (صلاحية عرض فقط)
        $applicant = Applicant::factory()->create();

        $payload = [
            'applicant_id' => $applicant->id,
            'SkillName' => 'Communication',
            'ProficiencyLevel' => 'Expert'
        ];

        $response = $this->postJson('/api/skills', $payload);

        // نتوقع 403 Forbidden لأن مدير الإدارة ليس من صلاحياته إضافة بيانات للمتقدم
        $response->assertStatus(403);
    }
}
