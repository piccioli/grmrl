<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'meeting_time' => '9:00',
            'meeting_place' => $this->faker->address(),
            'max_capacity' => 50,
            'is_active' => true,
        ];
    }
}
