<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Activity extends Model
{
    protected $fillable = ['name', 'description', 'meeting_time', 'meeting_place', 'max_capacity', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'max_capacity' => 'integer',
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
