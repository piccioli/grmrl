<?php

namespace Database\Factories;

use App\Models\Registration;
use Illuminate\Database\Eloquent\Factories\Factory;

class MinorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'registration_id' => Registration::factory(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'birth_date' => $this->faker->date('Y-m-d', '-5 years'),
            'is_cai_member' => false,
            'cai_section_id' => null,
            'fiscal_code' => null,
        ];
    }
}
