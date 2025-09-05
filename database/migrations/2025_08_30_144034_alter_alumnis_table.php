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
            $table->string('password')->nullable()->change();
            $table->string('alamat_jalan')->after('jurusan_id');
            $table->string('alamat_rt')->after('jurusan_id');
            $table->string('alamat_rw')->after('jurusan_id');
            $table->string('alamat_desa')->after('jurusan_id');
            $table->string('alamat_kelurahan')->after('jurusan_id');
            $table->string('alamat_kecamatan')->after('jurusan_id');
            $table->string('alamat_kode_pos')->after('jurusan_id');
            $table->string('tempat_lahir')->after('jurusan_id');
            $table->dropColumn('alamat')->after('jurusan_id');
            $table->dropColumn('tempat_kerja')->after('jurusan_id');
            $table->dropColumn('jabatan_kerja')->after('jurusan_id');
            $table->dropColumn('tempat_kuliah')->after('jurusan_id');
            $table->dropColumn('prodi_kuliah')->after('jurusan_id');
            $table->dropColumn('kesesuaian_kerja')->after('jurusan_id');
            $table->dropColumn('kesesuaian_kuliah')->after('jurusan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alumnis', function (Blueprint $table) {
            $table->string('tempat_kerja')->nullable();
            $table->string('jabatan_kerja')->nullable();
            $table->string('tempat_kuliah')->nullable();
            $table->string('prodi_kuliah')->nullable();
            $table->boolean('kesesuaian_kerja')->nullable();
            $table->boolean('kesesuaian_kuliah')->nullable();
        });
    }
};
