<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('interview_details', function (Blueprint $table) {
        $table->id();

        // 1. الربط برأس الفاتورة (المقابلة)
        $table->foreignId('interview_id')->constrained('interviews')->cascadeOnDelete();

        // 2. الربط بحركة التقديم (المرشح) - تأكد من اسم جدول حركات التقديم عندك
        $table->foreignId('application_id')->constrained('applications')->cascadeOnDelete();

        $table->time('InterviewTime'); // وقت المقابلة الدقيق
        $table->decimal('EvaluationScore', 8, 2)->nullable(); // الدرجة
        $table->string('Result')->default('Pending'); // النتيجة
        $table->text('Notes')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interview_details');
    }
};
