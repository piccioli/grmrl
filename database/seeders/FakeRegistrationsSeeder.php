<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\CaiSection;
use App\Models\Registration;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FakeRegistrationsSeeder extends Seeder
{
    public function run(): void
    {
        $activity = Activity::inRandomOrder()->first();

        if (! $activity) {
            $this->command->error('No activities found. Run ActivitySeeder first.');
            return;
        }

        $sectionId = CaiSection::inRandomOrder()->value('id');
        $i = 1;

        while ($activity->availableSpots() > 0) {
            $registration = Registration::create([
                'first_name'                    => fake()->firstName(),
                'last_name'                     => fake()->lastName(),
                'email'                         => fake()->unique()->safeEmail(),
                'phone'                         => fake()->numerify('3## ### ####'),
                'birth_date'                    => fake()->date('Y-m-d', '-20 years'),
                'is_cai_member'                 => true,
                'cai_section_id'                => $sectionId,
                'fiscal_code'                   => strtoupper(Str::random(16)),
                'activity_id'                   => $activity->id,
                'privacy_accepted'              => true,
                'photo_release_accepted'        => true,
                'rules_accepted'               => true,
                'weather_cancellation_accepted' => true,
                'equipment_check_accepted'      => true,
            ]);

            $registration->minors()->create([
                'first_name'  => fake()->firstName(),
                'last_name'   => fake()->lastName(),
                'birth_date'  => fake()->date('Y-m-d', '-10 years'),
                'is_cai_member' => false,
                'cai_section_id' => null,
                'fiscal_code'    => null,
            ]);

            $i++;
        }

        $this->command->info("Created " . ($i - 1) . " fake registrations for \"{$activity->name}\". isFull: " . ($activity->isFull() ? 'true' : 'false'));
    }
}
