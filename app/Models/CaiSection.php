<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CaiSection extends Model
{
    protected $fillable = ['code', 'name', 'region', 'province'];

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function minors(): HasMany
    {
        return $this->hasMany(Minor::class);
    }
}
