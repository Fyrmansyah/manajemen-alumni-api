<?php

use App\Models\Alumni;
use App\Models\JalurMasukKuliah;
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
        Schema::create('kuliahs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kampus');
            $table->string('prodi');
            $table->year('tahun_masuk');
            $table->year('tahun_lulus')->nullable();
            $table->boolean('sesuai_jurusan');
            $table->foreignIdFor(JalurMasukKuliah::class);
            $table->foreignIdFor(Alumni::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kuliahs');
    }
};
