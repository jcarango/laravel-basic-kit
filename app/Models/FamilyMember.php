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
        'birth_date',
        'age',
        'gender',
        'phone',
        'occupation',
        'education_level',
        'notes',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'age' => 'integer',
    ];

    protected static function booted()
    {
        static::saving(function ($familyMember) {
            if ($familyMember->birth_date) {
                $familyMember->age = \Carbon\Carbon::parse($familyMember->birth_date)->age;
            }
        });
    }


    public function suffragan(): BelongsTo
    {
        return $this->belongsTo(Suffragan::class);
    }
}
