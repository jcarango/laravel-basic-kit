<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SurveyResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'suffragan_id',
        'respondent_name',
        'document_number',
        'phone',
        'email',
        'address',
        'city_id',
        'latitude',
        'longitude',
        'converted_to_suffragan',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'converted_to_suffragan' => 'boolean',
    ];

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }


    public function suffragan(): BelongsTo
    {
        return $this->belongsTo(Suffragan::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(SurveyAnswer::class);
    }
}
