<?php

namespace Tests\Feature\Api;

use App\Models\Document;
use App\Models\Applicant;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\ApiTestCase;
use Laravel\Sanctum\Sanctum;

class DocumentTest extends ApiTestCase
{
    /**
     * اختبار عملية رفع مستند (مثلاً: سيرة ذاتية) وربطه بمتقدم.
     */
    public function test_can_upload_document_for_applicant(): void
    {
        // تسجيل الدخول كموظف توظيف
        Sanctum::actingAs($this->recruitmentOfficer);

        // محاكاة نظام التخزين المحلي حتى لا نمتلئ بملفات اختبارية حقيقية
        Storage::fake('local');

        // إنشاء متقدم وهمي لنربط الملف به
        $applicant = Applicant::factory()->create();

        // إنشاء ملف PDF وهمي بحجم 1024 كيلوبايت
        $file = UploadedFile::fake()->create('resume.pdf', 1024, 'application/pdf');

        $payload = [
            'documentable_id' => $applicant->id,
            'documentable_type' => Applicant::class,
            'file' => $file,
            'DocumentType' => 'CV',
            'name' => 'السيرة الذاتية لأحمد'
        ];

        $response = $this->postJson('/api/documents', $payload);

        $response->assertStatus(201);

        // التحقق من قاعدة البيانات
        $document = Document::first();
        $this->assertEquals('CV', $document->DocumentType);
        $this->assertEquals($applicant->id, $document->documentable_id);

        // التحقق الفيزيائي: هل تم حفظ الملف بالفعل في التخزين الوهمي؟
        Storage::disk('local')->assertExists($document->file_path);
    }

    /**
     * اختبار الحذف المرن للمستند.
     */
   public function test_can_soft_delete_document(): void
    {
        Sanctum::actingAs($this->recruitmentOfficer);

        $document = Document::factory()->create();

        $response = $this->deleteJson("/api/documents/{$document->id}");

        // تم تغيير 200 إلى 204 لأن الـ API الخاص بك يرجع 204 عند الحذف بنجاح
        $response->assertStatus(204);
        $this->assertSoftDeleted('documents', ['id' => $document->id]);
    }

    /**
     * اختبار حماية المسار: مدير الإدارة لا يملك صلاحية رفع مستندات.
     * (صلاحيته تنحصر في الرؤية فقط كما حددنا في الـ Seeder)
     */
    public function test_department_manager_cannot_upload_documents(): void
    {
        Sanctum::actingAs($this->departmentManager);

        $applicant = Applicant::factory()->create();
        $file = UploadedFile::fake()->create('test.pdf');

        $payload = [
            'documentable_id' => $applicant->id,
            'documentable_type' => Applicant::class,
            'file' => $file,
            'DocumentType' => 'National ID',
        ];

        $response = $this->postJson('/api/documents', $payload);

        $response->assertStatus(403);
    }
}
