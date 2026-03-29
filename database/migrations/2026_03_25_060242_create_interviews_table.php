<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();

            // ✅ تم التعديل: ربط الجلسة بجدول طلبات الاحتياج الوظيفي (job_requests)
            $table->foreignId('job_request_id')->constrained('job_requests')->cascadeOnDelete();

            // كود الموظف المُقيّم (حسب قاعدتك الصارمة: DECIMAL(18,0))
            $table->decimal('EmpCode', 18, 0);

            $table->date('InterviewDate'); // تاريخ يوم المقابلات
            $table->string('Location')->nullable(); // مكان المقابلة
            $table->string('Status')->default('Scheduled'); // حالة الجلسة
            $table->text('Notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interviews');
    }
};
