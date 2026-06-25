<?php

namespace Tests\Unit;

use App\Models\Activity;
use App\Models\Minor;
use App\Models\Registration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityTest extends TestCase
{
    use RefreshDatabase;

    public function test_available_spots_with_one_adult_and_two_minors(): void
    {
        $activity = Activity::factory()->create(['max_capacity' => 50]);
        $registration = Registration::factory()->create(['activity_id' => $activity->id]);
        Minor::factory()->count(2)->create(['registration_id' => $registration->id]);

        $this->assertEquals(47, $activity->availableSpots());
    }

    public function test_is_full_when_all_spots_occupied(): void
    {
        $activity = Activity::factory()->create(['max_capacity' => 50]);
        Registration::factory()->count(50)->create(['activity_id' => $activity->id]);

        $this->assertTrue($activity->isFull());
    }

    public function test_available_spots_without_registrations(): void
    {
        $activity = Activity::factory()->create(['max_capacity' => 50]);

        $this->assertEquals(50, $activity->availableSpots());
    }
}
