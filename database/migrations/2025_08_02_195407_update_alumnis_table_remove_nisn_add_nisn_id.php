<?php

use App\Models\Nisn;
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
            $table->dropColumn('nisn');
            $table->foreignIdFor(Nisn::class)->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alumnis', function (Blueprint $table) {
            $table->dropForeign(['nisn_id']);
            $table->dropColumn('nisn_id');
            $table->string('nisn')->after('id');
        });
    }
};
