<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_requests', function (Blueprint $table) {
            $table->id();
            // رقم الطلب كما اتفقنا
            $table->decimal('RequestNo', 18, 0)->unique();
            $table->foreignId('department_id')->constrained('departments')->restrictOnDelete();

            // معايير الفرز الأساسية التي سيطلبها المدير
            $table->string('RequiredDegreeLevel'); // مثل: بكالوريوس
            $table->string('RequiredMajor')->nullable(); // التخصص الدقيق
            $table->integer('RequiredYearsOfExperience')->default(0); // سنوات الخبرة المطلوبة

            // حالة طلب التوظيف
            $table->enum('Status', ['Open', 'Closed', 'Fulfilled'])->default('Open');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_requests');
    }
};
