<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();

            // هذا السطر ينشئ حقلين: documentable_id و documentable_type
            $table->morphs('documentable');

            $table->string('name')->nullable(); // اسم الملف الأصلي
            $table->string('file_path'); // مسار التخزين
            $table->string('DocumentType')->nullable(); // نوع المستند (CV, ID_Copy...) لسهولة الفلترة

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
