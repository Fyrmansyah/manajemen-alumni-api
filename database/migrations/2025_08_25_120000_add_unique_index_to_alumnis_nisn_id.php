<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('alumnis') && Schema::hasColumn('alumnis', 'nisn_id')) {
            // Tambahkan unique index jika belum ada
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexes = $sm->listTableIndexes('alumnis');
            if (!array_key_exists('alumnis_nisn_id_unique', $indexes)) {
                Schema::table('alumnis', function (Blueprint $table) {
                    $table->unique('nisn_id', 'alumnis_nisn_id_unique');
                });
            }
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
