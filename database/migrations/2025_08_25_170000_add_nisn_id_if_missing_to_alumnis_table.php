<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Fresh install case: table alumnis never had 'nisn' column, so previous adjust migration skipped adding nisn_id.
        if (Schema::hasTable('alumnis') && !Schema::hasColumn('alumnis', 'nisn_id')) {
            if (!Schema::hasTable('nisns')) {
                // Nisns table should already exist; if not, just skip to avoid breaking migrate.
                return; 
            }
            Schema::table('alumnis', function (Blueprint $table) {
                $table->foreignId('nisn_id')->nullable()->after('id')->constrained('nisns')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('alumnis') && Schema::hasColumn('alumnis', 'nisn_id')) {
            Schema::table('alumnis', function (Blueprint $table) {
                $table->dropForeign(['nisn_id']);
                $table->dropColumn('nisn_id');
            });
        }
    }
};
