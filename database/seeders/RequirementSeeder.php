<?php

namespace Database\Seeders;

use App\Models\Requirement;
use Illuminate\Database\Seeder;

class RequirementSeeder extends Seeder
{
    public function run(): void
    {
        $particulares = [
            'Mejoramiento de vivienda',
            'Mercado',
            'Subsidio',
            'Educación',
            'Salud',
            'Empleo',
            'Transporte',
            'Adulto mayor',
            'Discapacidad',
        ];

        foreach ($particulares as $item) {
            Requirement::firstOrCreate(
                ['name' => $item, 'type' => 'particular'],
                ['description' => "Requerimiento particular de {$item}", 'is_active' => true]
            );
        }

        $colectivos = [
            'Pavimentación',
            'Acueducto',
            'Alcantarillado',
            'Gas',
            'Internet',
            'Alumbrado',
            'Seguridad',
            'Escenarios deportivos',
            'Centros comunitarios',
        ];

        foreach ($colectivos as $item) {
            Requirement::firstOrCreate(
                ['name' => $item, 'type' => 'colectivo'],
                ['description' => "Requerimiento colectivo de {$item}", 'is_active' => true]
            );
        }
    }
}
