<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Requirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function suffragans(): BelongsToMany
    {
        return $this->belongsToMany(Suffragan::class, 'requirement_suffragan')
            ->withPivot(['status', 'notes'])
            ->withTimestamps();
    }
}
