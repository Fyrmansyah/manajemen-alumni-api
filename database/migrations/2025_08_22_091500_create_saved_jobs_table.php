<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('saved_jobs')) {
            Schema::create('saved_jobs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('alumni_id')->constrained('alumnis')->cascadeOnDelete();
                $table->foreignId('job_posting_id')->constrained('job_postings')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['alumni_id','job_posting_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_jobs');
    }
};
