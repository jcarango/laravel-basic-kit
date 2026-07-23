<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partido extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'resolución',
        'representantelegal',
        'email',
        'phone',
        'web',
        'logo',
        'is_visible',
    ];

    public function candidates()
    {
        return $this->belongsToMany(Candidate::class, 'candidate_partido');
    }

}
