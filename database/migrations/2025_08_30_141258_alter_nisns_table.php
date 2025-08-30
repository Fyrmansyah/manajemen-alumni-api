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
            $table->string('nama')->after('number');
            $table->string('nik')->after('nama');
            $table->date('tgl_lahir')->after('nik');
            $table->string('tempat_lahir')->after('tgl_lahir');
            $table->string('alamat')->after('tempat_lahir');
            $table->unsignedInteger('rt')->after('alamat');
            $table->unsignedInteger('rw')->after('rt');
            $table->string('kelurahan')->after('rw');
            $table->string('kecamatan')->after('kelurahan');
            $table->string('kode_pos')->after('kecamatan');
            $table->string('no_tlp')->after('kode_pos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nisns', function (Blueprint $table) {
            $table->dropColumn([
                'nama',
                'nik',
                'tgl_lahir',
                'tempat_lahir',
                'alamat',
                'rt',
                'rw',
                'kelurahan',
                'kecamatan',
                'kode_pos',
                'no_tlp',
            ]);
        });
    }
};
