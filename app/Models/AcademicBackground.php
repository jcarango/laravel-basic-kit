<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicBackground extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'currently_studying' => 'boolean',
            'is_verified' => 'boolean',
        ];
    }

    public function suffragan(): BelongsTo
    {
        return $this->belongsTo(Suffragan::class);
    }
}
