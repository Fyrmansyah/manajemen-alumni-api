<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('applications') && !Schema::hasColumn('applications', 'interview_media')) {
            Schema::table('applications', function (Blueprint $table) {
                $table->string('interview_media', 100)->nullable()->after('interview_details');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('applications') && Schema::hasColumn('applications', 'interview_media')) {
            Schema::table('applications', function (Blueprint $table) {
                $table->dropColumn('interview_media');
            });
        }
    }
};
