<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->timestamp('interview_at')->nullable()->after('reviewed_at');
            $table->string('interview_location')->nullable()->after('interview_at');
            $table->text('interview_details')->nullable()->after('interview_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['interview_at', 'interview_location', 'interview_details']);
        });
    }
};
