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
        $activities = Activity::inRandomOrder()->get();

        if ($activities->isEmpty()) {
            $this->command->error('No activities found. Run ActivitySeeder first.');

            return;
        }

        $sectionId = CaiSection::inRandomOrder()->value('id');

        // primo: saturato, secondo: vuoto, resto: random < 40 posti occupati
        foreach ($activities as $index => $activity) {
            if ($index === 0) {
                $this->fillToCapacity($activity, $sectionId);
            } elseif ($index === 1) {
                $this->command->info("Skipped \"{$activity->name}\" (lasciata vuota).");
            } else {
                $spots = rand(1, 39);
                $this->fillSpots($activity, $sectionId, $spots);
            }
        }
    }

    private function fillToCapacity(Activity $activity, ?int $sectionId): void
    {
        $count = 0;

        while ($activity->availableSpots() > 0) {
            $this->createRegistration($activity, $sectionId);
            $count++;
        }

        $this->command->info("Filled \"{$activity->name}\" to capacity ({$count} iscrizioni). isFull: true");
    }

    private function fillSpots(Activity $activity, ?int $sectionId, int $targetSpots): void
    {
        $count = 0;

        // ogni iscrizione occupa 2 posti (adulto + 1 minore), arrotondiamo per difetto
        $registrations = (int) floor($targetSpots / 2);

        for ($i = 0; $i < $registrations; $i++) {
            $this->createRegistration($activity, $sectionId);
            $count++;
        }

        $occupied = $activity->max_capacity - $activity->availableSpots();
        $this->command->info("Filled \"{$activity->name}\" with {$count} iscrizioni ({$occupied} posti occupati su {$activity->max_capacity}).");
    }

    private function createRegistration(Activity $activity, ?int $sectionId): void
    {
        $registration = Registration::create([
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('3## ### ####'),
            'birth_date' => fake()->date('Y-m-d', '-20 years'),
            'is_cai_member' => true,
            'cai_section_id' => $sectionId,
            'fiscal_code' => strtoupper(Str::random(16)),
            'activity_id' => $activity->id,
            'privacy_accepted' => true,
            'photo_release_accepted' => true,
            'rules_accepted' => true,
            'weather_cancellation_accepted' => true,
            'equipment_check_accepted' => true,
        ]);

        $registration->minors()->create([
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'birth_date' => fake()->date('Y-m-d', '-10 years'),
            'is_cai_member' => false,
            'cai_section_id' => null,
            'fiscal_code' => null,
        ]);
    }
}
