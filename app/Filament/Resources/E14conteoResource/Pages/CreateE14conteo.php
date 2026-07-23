<?php

namespace App\Filament\Resources\E14conteoResource\Pages;

use App\Filament\Resources\E14conteoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateE14conteo extends CreateRecord
{
    protected static string $resource = E14conteoResource::class;

    protected function afterCreate(): void
    {
        // Obtener los candidatos y sus votos del formulario
        $candidatesData = $this->data['candidates'];

        // Filtrar los candidatos para eliminar entradas vacías o con votos en cero
        $filteredCandidates = array_filter($candidatesData, function ($candidate) {
            return !empty($candidate['candidate_id']) && $candidate['votos'] > 0;
        });

        // Guardar en la tabla pivote solo los candidatos filtrados
        foreach ($filteredCandidates as $candidate) {
            $this->record->candidates()->attach($candidate['candidate_id'], ['votos' => $candidate['votos']]);
        }
    }
}
