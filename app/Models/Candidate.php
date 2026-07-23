<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'lastname',
        'email',
        'phone',
        'web',
        'country_id',
        'state_id',
        'city_id',
        'address',
        'photo',
        'is_visible',
        'partido_id',
        'cargo_aspira',
        'is_active',
        'is_principal',
        'is_opponent',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'is_active' => 'boolean',
        'is_principal' => 'boolean',
        'is_opponent' => 'boolean',
    ];

    public function scopeOpponents($query)
    {
        return $query->where('is_opponent', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }


    public function partido(): BelongsTo
    {
        return $this->belongsTo(Partido::class, 'partido_id');
    }


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

    public function campains()
    {
        return $this->belongsTo(Campain::class, 'campain_id');
    }

    public function leaders()
    {
        return $this->hasMany(Suffragan::class)->where('is_leader', true);
    }

    public function suffragans()
    {
        return $this->hasMany(Suffragan::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }


    public function getFullNameAttribute()
    {
        return $this->name . ' ' . $this->lastname;
    }
}