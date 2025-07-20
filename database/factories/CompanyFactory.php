<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Jurusan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        return [
            'company_name' => $this->faker->company(),
            'email' => $this->faker->unique()->companyEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'website' => $this->faker->url(),
            'description' => $this->faker->paragraph(3),
            'category_id' => Jurusan::inRandomOrder()->first()?->id,
            'established_year' => $this->faker->numberBetween(1980, 2020),
            'company_size' => $this->faker->randomElement(['1-10', '11-50', '51-100', '101-500', '500+']),
            'contact_person' => $this->faker->name(),
            'contact_person_phone' => $this->faker->phoneNumber(),
            'password' => Hash::make('password'),
            'status' => $this->faker->randomElement(['pending', 'aktif', 'inactive']),
            'is_approved' => $this->faker->boolean(70),
        ];
    }

    public function active(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'aktif',
                'is_approved' => true,
            ];
        });
    }

    public function pending(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
                'is_approved' => false,
            ];
        });
    }
}
