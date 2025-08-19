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
            $table->text('excerpt')->nullable()->after('content');
            $table->string('category')->default('info')->after('slug');
            $table->boolean('is_featured')->default(false)->after('status');
            $table->text('tags')->nullable()->after('views');
            $table->string('meta_description', 160)->nullable()->after('tags');
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
