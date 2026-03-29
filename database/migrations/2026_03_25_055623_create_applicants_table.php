<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->decimal('ApplicantNo', 18, 0)->unique(); // رقم داخلي فريد للمتقدم
            $table->decimal('NationalID', 18, 0)->unique(); // الرقم الوطني
            $table->decimal('ReferenceCode', 18, 0)->unique()->nullable(); // الرقم المرجعي لمتابعة الطلب

            // البيانات الشخصية
            $table->string('FirstName');
            $table->string('LastName');

            // بيانات التواصل (nullable في حال الإدخال الميداني السريع)
            $table->string('Email')->nullable()->unique();
            $table->string('PhoneNumber')->nullable();

            // العلاقات
            $table->foreignId('city_id')->constrained('cities')->restrictOnDelete();

            // مصدر الطلب
            $table->enum('ApplicationSource', ['Online', 'Manual'])->default('Online');

            // حالة الحساب/الملف بشكل عام
            $table->boolean('IsActive')->default(true);

            $table->timestamps();

            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};
