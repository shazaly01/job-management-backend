<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applicant_qualifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained('applicants')->cascadeOnDelete();

            $table->string('DegreeLevel'); // مثل: بكالوريوس، ماجستير، دبلوم
            $table->string('Major'); // التخصص الدقيق: هندسة برمجيات، محاسبة
            $table->year('GraduationYear'); // سنة التخرج
            $table->string('UniversityOrInstitute'); // الجهة المانحة للشهادة
            $table->string('GPA_or_Grade')->nullable(); // المعدل أو التقدير (ممتاز، جيد جداً)

            $table->timestamps();
            $table->softDeletes(); // إضافة الحذف المرن
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applicant_qualifications');
    }
};
