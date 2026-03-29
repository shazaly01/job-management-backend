<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::table('job_requests', function (Blueprint $table) {
        // 1. إضافة حقل وصف الوظيفة (الذي تسبب في الخطأ)
        if (!Schema::hasColumn('job_requests', 'JobDescription')) {
            $table->text('JobDescription')->nullable()->after('RequiredYearsOfExperience');
        }

        // 2. إضافة حقل الـ slug (للتأكد من وجوده أيضاً)
        if (!Schema::hasColumn('job_requests', 'slug')) {
            $table->string('slug')->unique()->nullable()->after('RequestNo');
        }
    });
}

public function down(): void
{
    Schema::table('job_requests', function (Blueprint $table) {
        $table->dropColumn(['JobDescription', 'slug']);
    });
}
};
