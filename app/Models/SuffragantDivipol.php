<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuffragantDivipol extends Model
{
    use HasFactory;

    // Define la tabla asociada (opcional si sigue la convención de nombres)
    protected $table = 'suffragant_divipol';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'candidato_id',
        'divipol_id',
        'valor',
    ];

    /**
     * Relación con el modelo Candidate (Candidato)
     * Un registro de SuffragantDivipol pertenece a un candidato.
     */
    public function candidate()
    {
        return $this->belongsTo(Candidate::class, 'candidato_id');
    }

    /**
     * Relación con el modelo Divipol
     * Un registro de SuffragantDivipol pertenece a una división política (divipol).
     */
    public function divipol()
    {
        return $this->belongsTo(Divipol::class, 'divipol_id');
    }

    /**
     * Relación con el modelo E14Conteo
     * Un registro de SuffragantDivipol puede estar relacionado con un conteo en E14.
     */
    public function e14conteo()
    {
        return $this->belongsTo(E14Conteo::class, 'suffragan_divipol_id');
    }
}
