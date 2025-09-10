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
        Schema::table('alumnis', function (Blueprint $table) {
            if (!Schema::hasColumn('alumnis', 'status_kerja')) {
                $table->enum('status_kerja', ['bekerja', 'kuliah', 'wirausaha', 'menganggur', 'belum_diisi'])
                      ->default('belum_diisi')
                      ->after('pengalaman_kerja')
                      ->comment('Status pekerjaan alumni saat ini');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alumnis', function (Blueprint $table) {
            if (Schema::hasColumn('alumnis', 'status_kerja')) {
                $table->dropColumn('status_kerja');
            }
        });
    }
};
