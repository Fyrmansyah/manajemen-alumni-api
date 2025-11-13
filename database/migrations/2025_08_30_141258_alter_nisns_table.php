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
        Schema::table('nisns', function (Blueprint $table) {
            if (!Schema::hasColumn('nisns', 'nama')) {
                $table->string('nama')->nullable()->after('number');
            }
            if (!Schema::hasColumn('nisns', 'nik')) {
                $table->string('nik')->nullable()->after('nama');
            }
            if (!Schema::hasColumn('nisns', 'tgl_lahir')) {
                $table->date('tgl_lahir')->nullable()->after('nik');
            }
            if (!Schema::hasColumn('nisns', 'tempat_lahir')) {
                $table->string('tempat_lahir')->nullable()->after('tgl_lahir');
            }
            if (!Schema::hasColumn('nisns', 'alamat')) {
                $table->string('alamat')->nullable()->after('tempat_lahir');
            }
            if (!Schema::hasColumn('nisns', 'rt')) {
                $table->unsignedInteger('rt')->nullable()->after('alamat');
            }
            if (!Schema::hasColumn('nisns', 'rw')) {
                $table->unsignedInteger('rw')->nullable()->after('rt');
            }
            if (!Schema::hasColumn('nisns', 'kelurahan')) {
                $table->string('kelurahan')->nullable()->after('rw');
            }
            if (!Schema::hasColumn('nisns', 'kecamatan')) {
                $table->string('kecamatan')->nullable()->after('kelurahan');
            }
            if (!Schema::hasColumn('nisns', 'kode_pos')) {
                $table->string('kode_pos')->nullable()->after('kecamatan');
            }
            if (!Schema::hasColumn('nisns', 'no_tlp')) {
                $table->string('no_tlp')->nullable()->after('kode_pos');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nisns', function (Blueprint $table) {
            foreach (['no_tlp','kode_pos','kecamatan','kelurahan','rw','rt','alamat','tempat_lahir','tgl_lahir','nik','nama'] as $col) {
                if (Schema::hasColumn('nisns', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
