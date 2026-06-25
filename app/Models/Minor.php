<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Minor extends Model
{
    use HasFactory;
    protected $fillable = [
        'registration_id',
        'first_name',
        'last_name',
        'birth_date',
        'is_cai_member',
        'cai_section_id',
        'fiscal_code',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_cai_member' => 'boolean',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function caiSection(): BelongsTo
    {
        return $this->belongsTo(CaiSection::class);
    }
}
