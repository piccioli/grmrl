<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'detailed_description', 'meeting_time', 'meeting_place', 'max_capacity', 'is_active', 'latitude', 'longitude', 'difficulty', 'elevation_gain', 'trail_length', 'water_description', 'itinerary_description', 'image_url'];

    protected $casts = [
        'is_active' => 'boolean',
        'max_capacity' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function availableSpots(): int
    {
        $occupied = $this->registrations()
            ->withCount('minors')
            ->get()
            ->sum(fn ($r) => 1 + $r->minors_count);

        return max(0, $this->max_capacity - $occupied);
    }

    public function isFull(): bool
    {
        return $this->availableSpots() <= 0;
    }
}
