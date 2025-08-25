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
            // Add missing profile fields
            if (!Schema::hasColumn('alumnis', 'nama_lengkap')) {
                $table->string('nama_lengkap')->nullable()->after('nama');
            }
            if (!Schema::hasColumn('alumnis', 'phone')) {
                $table->string('phone')->nullable()->after('no_tlp');
            }
            if (!Schema::hasColumn('alumnis', 'tanggal_lahir')) {
                $table->date('tanggal_lahir')->nullable()->after('tgl_lahir');
            }
            if (!Schema::hasColumn('alumnis', 'jenis_kelamin')) {
                $table->enum('jenis_kelamin', ['L', 'P'])->nullable()->after('tanggal_lahir');
            }
            // Jangan tambah ulang kolom 'nisn' lama karena sudah digantikan oleh 'nisn_id'
            if (!Schema::hasColumn('alumnis', 'pengalaman_kerja')) {
                $table->text('pengalaman_kerja')->nullable()->after('alamat');
            }
            if (!Schema::hasColumn('alumnis', 'keahlian')) {
                $table->text('keahlian')->nullable()->after('pengalaman_kerja');
            }
            if (!Schema::hasColumn('alumnis', 'whatsapp_notifications')) {
                $table->boolean('whatsapp_notifications')->default(false)->after('keahlian');
            }
            if (!Schema::hasColumn('alumnis', 'foto')) {
                $table->string('foto')->nullable()->after('whatsapp_notifications');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alumnis', function (Blueprint $table) {
            $table->dropColumn([
                'nama_lengkap',
                'phone', 
                'tanggal_lahir',
                'jenis_kelamin',
                'pengalaman_kerja',
                'keahlian',
                'whatsapp_notifications',
                'foto'
            ]);
        });
    }
};
