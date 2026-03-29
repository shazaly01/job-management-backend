<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            // رقم الحركة لعملية التقديم
            $table->decimal('TransactionNo', 18, 0)->unique();

            $table->foreignId('applicant_id')->constrained('applicants')->cascadeOnDelete();

            // nullable لأن المتقدم قد يضع ملفه في النظام دون التقديم على شاغر محدد (General Pool)
            $table->foreignId('job_request_id')->nullable()->constrained('job_requests')->restrictOnDelete();

            // مسار المتقدم في التوظيف
            $table->enum('ApplicationStatus', ['Pending', 'Shortlisted', 'Interview', 'Accepted', 'Rejected'])->default('Pending');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
