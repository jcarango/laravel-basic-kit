<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'name',
        'code',
        'phone_code',
        'currency',
        'flag',
        'language',
        'region',
    ];
    
    public function states()
    {
        return $this->hasMany(State::class);
    }
}
