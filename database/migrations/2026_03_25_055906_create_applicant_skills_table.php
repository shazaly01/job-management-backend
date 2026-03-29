<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applicant_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained('applicants')->cascadeOnDelete();

            $table->string('SkillName'); // اسم المهارة أو الدورة
            $table->enum('ProficiencyLevel', ['Beginner', 'Intermediate', 'Advanced', 'Expert'])->default('Intermediate'); // مستوى الإجادة

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applicant_skills');
    }
};
