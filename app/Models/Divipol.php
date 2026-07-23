<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Divipol extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'dep',
        'mun',
        'zon',
        'pto',
        'departamento',
        'municipio',
        'nom_puesto',
        'direccion',
        'ind_mesa',
        'categoria',
        'mujeres',
        'hombres',
        'potencial',
        'mesas_totales',
        'jal',
        'nom_jal'       
    ];

    public function suffragans(): BelongsToMany
    {
        return $this->belongsToMany(Suffragan::class, 'divipol_suffragan');
    }
}
