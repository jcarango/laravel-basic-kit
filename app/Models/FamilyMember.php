<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'suffragan_id',
        'name',
        'document_number',
        'relationship',
        'age',
        'gender',
        'phone',
        'occupation',
        'education_level',
        'notes',
    ];

    public function suffragan(): BelongsTo
    {
        return $this->belongsTo(Suffragan::class);
    }
}
