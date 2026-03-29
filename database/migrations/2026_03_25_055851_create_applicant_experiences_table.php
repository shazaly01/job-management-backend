<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applicant_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained('applicants')->cascadeOnDelete();

            $table->string('JobTitle'); // المسمى الوظيفي
            $table->string('CompanyName'); // جهة العمل
            $table->date('StartDate'); // تاريخ البداية
            $table->date('EndDate')->nullable(); // تاريخ النهاية (Nullable تعني أنه لا يزال يعمل هنا)
            $table->text('JobDescription')->nullable(); // وصف مختصر للمهام (اختياري ولكنه مفيد للفرز)

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applicant_experiences');
    }
};
