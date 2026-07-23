<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Observation extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'description',
        'input',
        'suffragan_id'
    ];

    public function suffragan(): BelongsTo
    {
        return $this->belongsTo(Suffragan::class, 'suffragan_id');
    }
}
