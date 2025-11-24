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
            $table->text('alamat')->nullable()->after('phone');
            $table->string('tempat_kerja')->nullable()->after('tempat_lahir');
            $table->string('jabatan_kerja')->nullable()->after('tempat_kerja');
            $table->string('tempat_kuliah')->nullable()->after('jabatan_kerja');
            $table->string('prodi_kuliah')->nullable()->after('tempat_kuliah');
            $table->boolean('kesesuaian_kerja')->nullable()->after('prodi_kuliah');
            $table->boolean('kesesuaian_kuliah')->nullable()->after('kesesuaian_kerja');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alumnis', function (Blueprint $table) {
            $table->dropColumn([
                'alamat',
                'tempat_kerja',
                'jabatan_kerja',
                'tempat_kuliah',
                'prodi_kuliah',
                'kesesuaian_kerja',
                'kesesuaian_kuliah'
            ]);
        });
    }
};
