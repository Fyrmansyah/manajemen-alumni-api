<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'freelance' to the job_postings type enum
        DB::statement("ALTER TABLE job_postings MODIFY COLUMN type ENUM('full_time', 'part_time', 'contract', 'freelance', 'internship') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'freelance' from the job_postings type enum
        DB::statement("ALTER TABLE job_postings MODIFY COLUMN type ENUM('full_time', 'part_time', 'contract', 'internship') NOT NULL");
    }
};
