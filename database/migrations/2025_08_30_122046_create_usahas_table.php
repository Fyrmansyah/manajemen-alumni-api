<?php

use App\Models\Alumni;
use App\Models\KepemilikanUsaha;
use App\Models\RangeLaba;
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
        Schema::create('usahas', function (Blueprint $table) {
            $table->id();
            $table->date('tgl_mulai');
            $table->date('tgl_selesai')->nullable();
            $table->string('nama_perusahaan');
            $table->string('bidang');
            $table->foreignIdFor(KepemilikanUsaha::class);
            $table->unsignedInteger('jml_karyawan')->default(0);
            $table->foreignIdFor(RangeLaba::class);
            $table->foreignIdFor(Alumni::class);
            $table->boolean('sesuai_jurusan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usahas');
    }
};
