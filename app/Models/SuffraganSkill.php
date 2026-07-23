<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuffraganSkill extends Pivot
{
    protected $table = 'suffragan_skills';

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }

    public function suffragan(): BelongsTo
    {
        return $this->belongsTo(Suffragan::class);
    }
}
