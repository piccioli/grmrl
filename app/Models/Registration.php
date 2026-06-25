<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Registration extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'birth_date',
        'is_cai_member',
        'cai_section_id',
        'fiscal_code',
        'activity_id',
        'privacy_accepted',
        'photo_release_accepted',
        'rules_accepted',
        'weather_cancellation_accepted',
        'equipment_check_accepted',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_cai_member' => 'boolean',
        'privacy_accepted' => 'boolean',
        'photo_release_accepted' => 'boolean',
        'rules_accepted' => 'boolean',
        'weather_cancellation_accepted' => 'boolean',
        'equipment_check_accepted' => 'boolean',
    ];

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function caiSection(): BelongsTo
    {
        return $this->belongsTo(CaiSection::class);
    }

    public function minors(): HasMany
    {
        return $this->hasMany(Minor::class);
    }
}
