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
            if (!Schema::hasColumn('news', 'excerpt')) {
                $table->text('excerpt')->nullable()->after('content');
            }
            if (!Schema::hasColumn('news', 'category')) {
                $table->string('category')->default('info')->after('slug');
            }
            if (!Schema::hasColumn('news', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('status');
            }
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
            $table->dropColumn(['excerpt', 'category', 'is_featured', 'tags', 'meta_description']);
        });
    }
};
