<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Campain extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'web',
        'country_id',
        'state_id',
        'city_id',
        'address',
        'logo',
        'is_visible',
        'partido_id',
    ];


    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function partido(): BelongsTo
    {
        return $this->belongsTo(Partido::class);
    }

    public function candidates()
    {
        return $this->hasMany(Candidate::class);
    }
}
