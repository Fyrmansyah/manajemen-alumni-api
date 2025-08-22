<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'industry')) {
                $table->string('industry')->nullable()->after('description');
            }
            if (!Schema::hasColumn('companies', 'contact_position')) {
                $table->string('contact_position')->nullable()->after('contact_person_phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'industry')) {
                $table->dropColumn('industry');
            }
            if (Schema::hasColumn('companies', 'contact_position')) {
                $table->dropColumn('contact_position');
            }
        });
    }
};
