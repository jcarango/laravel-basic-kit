<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Skill extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function suffragans(): BelongsToMany
    {
        return $this->belongsToMany(Suffragan::class, 'suffragan_skills')
                    ->withPivot(['level', 'years_experience'])
                    ->withTimestamps();
    }
}
