<?php

namespace Database\Factories;

use App\Models\Job;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobFactory extends Factory
{
    protected $model = Job::class;

    public function definition(): array
    {
        $jobTitles = [
            'Software Developer',
            'Web Developer',
            'Mobile App Developer',
            'Data Analyst',
            'UI/UX Designer',
            'Digital Marketing Specialist',
            'Network Administrator',
            'Database Administrator',
            'Cyber Security Analyst',
            'IT Support Specialist',
            'Quality Assurance Tester',
            'Project Manager',
            'Business Analyst',
            'DevOps Engineer',
            'System Administrator'
        ];

        $locations = [
            'Jakarta',
            'Surabaya',
            'Bandung',
            'Yogyakarta',
            'Medan',
            'Semarang',
            'Malang',
            'Denpasar',
            'Makassar',
            'Palembang'
        ];

        return [
            'company_id' => Company::active()->inRandomOrder()->first()?->id ?? Company::factory()->active(),
            'title' => $this->faker->randomElement($jobTitles),
            'description' => $this->faker->paragraphs(3, true),
            'requirements' => $this->faker->paragraphs(2, true),
            'location' => $this->faker->randomElement($locations),
            'type' => $this->faker->randomElement(['full_time', 'part_time', 'contract', 'internship']),
            'salary_min' => $this->faker->numberBetween(3000000, 8000000),
            'salary_max' => $this->faker->numberBetween(8000000, 15000000),
            'application_deadline' => $this->faker->dateTimeBetween('+1 week', '+3 months'),
            'status' => $this->faker->randomElement(['draft', 'active', 'closed']),
            'positions_available' => $this->faker->numberBetween(1, 5),
        ];
    }

    public function active(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'active',
            ];
        });
    }

    public function draft(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'draft',
            ];
        });
    }
}
