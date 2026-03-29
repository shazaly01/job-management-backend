<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            // إضافة حقل التوكن ليكون فريداً ويقبل الفراغ (للمتقدمين القدامى إن وجدوا)
            $table->string('resume_token')->unique()->nullable()->after('ApplicationSource');
        });
    }

    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropColumn('resume_token');
        });
    }
};
