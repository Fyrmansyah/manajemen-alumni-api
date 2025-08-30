<?php

namespace Database\Seeders;

use App\Models\Nisn;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NisnSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Nisn::create([
            "number" => "111",
            "nama" => "Nama Test",
            "nik" => "111",
            "tgl_lahir" => "2003-10-19",
            "tempat_lahir" => "SIDOARJO",
            "alamat" => "alamat test",
            "rt" => 1,
            "rw" => 1,
            "kelurahan" => "Kel. test",
            "kecamatan" => "Kecamatan test",
            "kode_pos" => "61262",
            "no_tlp" => "111",
        ]);
        Nisn::create([
            "number" => "0032984381",
            "nama" => "A'ISYAH MUTIARA MAHARANI",
            "nik" => "3578045410030004",
            "tgl_lahir" => "2003-10-14",
            "tempat_lahir" => "SURABAYA",
            "alamat" => "Perum Taman Sidorejo",
            "rt" => 0,
            "rw" => 0,
            "kelurahan" => "Desa/Kel. Sidorejo",
            "kecamatan" => "Sidorejo",
            "kode_pos" => "61262",
            "no_tlp" => "0882009136470",
        ]);
        Nisn::create([
            "number" => "0053635416",
            "nama" => "AALIYAH AZKA FAIRUZZA",
            "nik" => "3515134804050006",
            "tgl_lahir" => "2005-04-08",
            "tempat_lahir" => "SURABAYA",
            "alamat" => "WISMA TROSOBO IV / 08",
            "rt" => 7,
            "rw" => 3,
            "kelurahan" => "Desa/Kel. Trosobo",
            "kecamatan" => "Trosobo",
            "kode_pos" => "61257",
            "no_tlp" => "089676217318",
        ]);
    }
}
