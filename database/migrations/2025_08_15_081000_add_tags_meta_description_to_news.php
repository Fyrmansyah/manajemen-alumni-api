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
        Schema::table('news', function (Blueprint $table) {
            if (!Schema::hasColumn('news', 'tags')) {
                $table->text('tags')->nullable()->after('views');
            }
            if (!Schema::hasColumn('news', 'meta_description')) {
                $table->string('meta_description', 160)->nullable()->after('tags');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            if (Schema::hasColumn('news', 'meta_description')) {
                $table->dropColumn('meta_description');
            }
            if (Schema::hasColumn('news', 'tags')) {
                $table->dropColumn('tags');
            }
        });
    }
};
