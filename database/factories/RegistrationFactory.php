<?php

namespace Database\Factories;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Factories\Factory;

class RegistrationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'birth_date' => $this->faker->date('Y-m-d', '-18 years'),
            'is_cai_member' => false,
            'cai_section_id' => null,
            'fiscal_code' => strtoupper($this->faker->bothify('??????##?###?###')),
            'activity_id' => Activity::factory(),
            'privacy_accepted' => true,
            'photo_release_accepted' => true,
            'rules_accepted' => true,
            'weather_cancellation_accepted' => true,
            'equipment_check_accepted' => true,
        ];
    }
}
