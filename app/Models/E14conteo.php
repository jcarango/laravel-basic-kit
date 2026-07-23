<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class E14conteo extends Model
{
    use LogsActivity;

    protected $fillable = [
        'mesa',
        'total_sufragantes_e11',
        'total_votos_urna',
        'total_votos_incinerados',
        'votos_nulos',
        'votos_no_marcados',
        'votos_en_blanco',
        'total_votos_mesa',
        'photo',
        'hubo_reconteo',
        'divipol_id',
        'user_id',
        'ai_match_results',
        'ai_matched',
    ];

    protected $casts = [
        'ai_match_results' => 'array',
        'ai_matched' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('e14conteo')
            ->logOnly(['mesa', 'total_sufragantes_e11', 'total_votos_urna', 'total_votos_incinerados', 'votos_nulos', 'votos_no_marcados', 'votos_en_blanco', 'total_votos_mesa', 'divipol_id', 'user_id'])
            ->logOnlyDirty();
    }

    public function divipol(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Divipol::class);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function candidates(): BelongsToMany
    {
        return $this->belongsToMany(Candidate::class, 'candidate_e14conteo')->withPivot('votos')->withTimestamps();
    }

    public function candidate()
    {
        return $this->belongsToMany(Candidate::class, 'candidate_e14conteo')->withPivot('votos')->withTimestamps();
    }
}
