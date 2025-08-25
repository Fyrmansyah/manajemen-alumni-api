<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('alumnis') || !Schema::hasColumn('alumnis', 'nisn_id')) {
            return;
        }

        // Coba tambahkan index; abaikan jika sudah ada
        try {
            Schema::table('alumnis', function (Blueprint $table) {
                $table->unique('nisn_id', 'alumnis_nisn_id_unique');
            });
        } catch (Throwable $e) {
            // Kemungkinan index sudah ada; diamkan
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('alumnis') && Schema::hasColumn('alumnis', 'nisn_id')) {
            Schema::table('alumnis', function (Blueprint $table) {
                $table->dropUnique('alumnis_nisn_id_unique');
            });
        }
    }
};
