<?php

use App\Models\Alumni;
use App\Models\DurasiKerja;
use App\Models\JenisPerusahaan;
use App\Models\MasaTungguKerja;
use App\Models\RangeGaji;
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
        Schema::create('kerjas', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Alumni::class);
            $table->foreignIdFor(MasaTungguKerja::class);
            $table->string('nama_perusahaan');
            $table->string('alamat_perusahaan');
            $table->foreignIdFor(JenisPerusahaan::class);
            $table->foreignIdFor(DurasiKerja::class);
            $table->foreignIdFor(RangeGaji::class);
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->boolean('sesuai_jurusan');
            $table->string('jabatan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kerjas');
    }
};
