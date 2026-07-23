<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resume extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_available_for_committees' => 'boolean',
            'profile_score' => 'integer',
        ];
    }

    public function suffragan(): BelongsTo
    {
        return $this->belongsTo(Suffragan::class);
    }
}
