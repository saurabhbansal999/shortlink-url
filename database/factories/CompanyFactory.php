<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_name'  => $this->faker->company(),
            'company_email' => $this->faker->unique()->companyEmail(),
            'company_phone' => $this->faker->phoneNumber(),
        ];
    }
}
