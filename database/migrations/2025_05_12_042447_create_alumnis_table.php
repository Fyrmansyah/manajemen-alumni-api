<?php

use App\Models\Jurusan;
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
        Schema::create('alumnis', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->date('tgl_lahir');
            $table->year('tahun_mulai');
            $table->year('tahun_lulus');
            $table->string('no_tlp');
            $table->string('email');
            $table->string('password')->nullable();
            $table->string('alamat');
            $table->string('tempat_kerja')->nullable();
            $table->string('jabatan_kerja')->nullable();
            $table->string('tempat_kuliah')->nullable();
            $table->string('prodi_kuliah')->nullable();
            $table->boolean('kesesuaian_kerja')->nullable();
            $table->boolean('kesesuaian_kuliah')->nullable();
            $table->foreignIdFor(Jurusan::class)->nullable()->constrained()->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumnis');
    }
};
