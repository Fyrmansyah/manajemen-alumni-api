<?php

namespace Database\Factories;

use App\Models\Jurusan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Alumni>
 */
class AlumniFactory extends Factory
{
    static $jurusanIds = [];
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        if (!self::$jurusanIds) {
            self::$jurusanIds = Jurusan::query()->pluck('id')->toArray();
        }

        return [
            'nama' => $this->faker->name(),
            'tgl_lahir' => $this->faker->date(),
            'tahun_mulai' => $this->faker->randomElement([2020, 2021, 2022, 2023, 2024, 2025]),
            'tahun_lulus' => $this->faker->randomElement([2020, 2021, 2022, 2023, 2024, 2025]),
            'no_tlp' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => 'password',
            'alamat' => $this->faker->address(),
            'tempat_kerja' => $this->faker->optional()->company(),
            'jabatan_kerja' => $this->faker->optional()->jobTitle(),
            'tempat_kuliah' => $this->faker->randomElement(['Institut Sepuluh Nopember', 'Universitas Negeri Surabaya', 'Politeknik Elektronika Surabaya', 'Universitas Indonesia', 'Institut Teknologi Bandung', 'Harvard']), // or use ->university() if using faker extension
            'prodi_kuliah' => $this->faker->randomElement(['Teknik Informatika', 'Sistem Informasi', 'Psikologi', 'Hukum', 'Elektro', 'Akuntansi', 'Pariwisata']),
            'kesesuaian_kerja' => $this->faker->optional()->boolean(),
            'kesesuaian_kuliah' => $this->faker->optional()->boolean(),
            'photo' => $this->faker->optional()->imageUrl(300, 300),
            'jurusan_id' => $this->faker->randomElement(self::$jurusanIds)
        ];
    }
}
