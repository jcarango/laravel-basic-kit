<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Suffragan extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'lastname',
        'email',
        'phone',
        'documentationtype',
        'documentationnumber',
        'latitude',
        'longitude',
        'country_id',
        'state_id',
        'city_id',
        'address',
        'profession',
        'facebook',
        'twitter',
        'instagram',
        'linkedin',
        'photo',
        'categories_id',
        'mesa',
        'divipol_id',
        'suffragan_id',
        'user_id',
        'user_agent', 
        'platform',
        'language',
        'screen_resolution',
        'timezone',
        'latitude_event',
        'longitude_event',
        'habeas_data_accepted',
        'event_id',
        'is_leader',
        'is_witness',
        'candidate_id',
        'consecutivo',
        'characterization_date',
        'vereda',
        'corregimiento',
        'property_name',
        'total_area',
        'available_area',
        'cadastral_status',
        'is_project_beneficiary',
        'project_name',
        'has_disability',
        'disability_type',
        'gender',
        'livestock_count',
        'species',
        'unit_of_measure',
        'population_groups',
        'belongs_to_association',
        'association_name',
        'knows_lamb_project',
        'lamb_project_source',
    ];

    protected $casts = [
        'is_leader' => 'boolean',
        'is_witness' => 'boolean',
        'habeas_data_accepted' => 'boolean',
        'is_project_beneficiary' => 'boolean',
        'has_disability' => 'boolean',
        'belongs_to_association' => 'boolean',
        'knows_lamb_project' => 'boolean',
        'characterization_date' => 'date',
        'population_groups' => 'array',
        'total_area' => 'decimal:2',
        'available_area' => 'decimal:2',
        'livestock_count' => 'integer',
    ];


    public function divipols(): BelongsToMany
    {
        return $this->belongsToMany(Divipol::class, 'suffragan_divipols');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'categories_id');
    }

    public function divipol(): BelongsTo
    {
        return $this->belongsTo(Divipol::class, 'divipol_id');
    }

    public function observations()
    {
        return $this->hasMany(Observation::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    // Método para obtener la latitud y longitud de la dirección
    public function setCoordinatesFromAddress()
    {
        $address = $this->address;
        $apiUrl = 'https://nominatim.openstreetmap.org/search';

        $response = Http::get($apiUrl, [
            'q' => $address,
            'format' => 'json',
            'addressdetails' => 1,
            'limit' => 1,
        ]);

        $data = $response->json();

        if (!empty($data)) {
            $this->latitude = $data[0]['lat'];
            $this->longitude = $data[0]['lon'];
        }
    }

    protected $appends = [
        'votodepartamento', 
        'votomunicipio', 
        'votopuesto', 
        'votodireccion',
        'location'
    ];

    public function getLocationAttribute()
    {
        return [
            "lat" => (float) $this->latitude,
            "lng" => (float) $this->longitude,
        ];
    }

    public function setLocationAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['latitude'] = $value['lat'];
            $this->attributes['longitude'] = $value['lng'];
        }
    }

    public function getVotodepartamentoAttribute()
    {
        return $this->divipol?->departamento;
    }

    public function getVotomunicipioAttribute()
    {
        return $this->divipol?->municipio;
    }

    public function getVotopuestoAttribute()
    {
        return $this->divipol?->nom_puesto;
    }

    public function getVotodireccionAttribute()
    {
        return $this->divipol?->direccion;
    }

    public function candidateVotes()
    {
        return $this->belongsToMany(Candidate::class, 'candidate_suffragan_votes')
                    ->withPivot([
                        'votes', 
                        'votos_blanco', 
                        'votos_nulos', 
                        'votos_no_marcados', 
                        'total_votantes_e11', 
                        'total_votos_urna', 
                        'total_incinerados'
                    ])
                    ->withTimestamps();
    }

    public function candidateSuffraganE14conteos()
    {
        return $this->hasMany(CandidateSuffraganE14conteo::class);
    }

    public function leader()
    {
        return $this->belongsTo(Suffragan::class, 'suffragan_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function eventsCreatedByMe()
    {
        return $this->hasMany(Event::class, 'suffragan_id');
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_suffragan')
                    ->withPivot('attended_at')
                    ->withTimestamps();
    }

    public function resume(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Resume::class);
    }

    public function familyMembers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FamilyMember::class);
    }

    public function requirements(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Requirement::class, 'requirement_suffragan')
            ->withPivot(['status', 'notes'])
            ->withTimestamps();
    }



    public function experience(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(WorkExperience::class)->orderBy('end_date', 'desc');
    }

    public function education(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AcademicBackground::class)->orderBy('end_date', 'desc');
    }

    public function skills(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'suffragan_skills')
                    ->using(SuffraganSkill::class)
                    ->withPivot(['level', 'years_experience'])
                    ->withTimestamps();
    }

    public function suffraganSkills(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SuffraganSkill::class);
    }

    public function politicalCommittees(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(PoliticalCommittee::class, 'committee_suffragan', 'suffragan_id', 'committee_id')
                    ->using(CommitteeSuffragan::class)
                    ->withPivot(['role', 'joined_at'])
                    ->withTimestamps();
    }

    public function committeeSuffragans(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CommitteeSuffragan::class, 'suffragan_id');
    }

    public function leaderResources(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(LeaderResource::class, 'leader_id');
    }

    public function scopeAvailableForCommittees($query)
    {
        return $query->whereHas('resume', function($q) {
            $q->where('is_available_for_committees', true);
        });
    }

    public function calculateProfileScore(): void
    {
        if (! $this->exists) {
            return;
        }

        if (! $this->resume) {
            \App\Models\Resume::withoutEvents(function () {
                $this->resume()->create([
                    'profile_score' => 0,
                    'is_available_for_committees' => false,
                ]);
            });
            $this->load('resume');
        }

        $score = 0;
        
        // Educación
        $graduados = $this->education()->where('status', 'Graduado')->count();
        $score += ($graduados * 15);
        
        // Experiencia
        $aniosExp = 0;
        foreach($this->experience as $exp) {
            if ($exp->start_date) {
                $start = \Carbon\Carbon::parse($exp->start_date);
                $end = $exp->currently_working ? now() : ($exp->end_date ? \Carbon\Carbon::parse($exp->end_date) : null);
                
                if ($end) {
                    $aniosExp += $start->diffInYears($end);
                }
            }
        }
        $score += ($aniosExp * 5);

        // Habilidades
        $expertoSkills = $this->skills()->wherePivot('level', 'Experto')->count();
        $avanzadoSkills = $this->skills()->wherePivot('level', 'Avanzado')->count();
        $score += ($expertoSkills * 10) + ($avanzadoSkills * 5);

        \App\Models\Resume::withoutEvents(function () use ($score) {
            $this->resume->update(['profile_score' => $score]);
        });
    }
}
