<?php

namespace App\Filament\Resources\E14conteoResource\Pages;

use App\Filament\Resources\E14conteoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditE14conteo extends EditRecord
{
    protected static string $resource = E14conteoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['candidates'] = $this->record->candidates->map(function ($candidate) {
            return [
                'candidate_id' => $candidate->id,
                'votos' => $candidate->pivot->votos,
            ];
        })->toArray();

        return $data;
    }

    protected function afterSave(): void
    {
        // Obtener los candidatos y sus votos del formulario
        $candidatesData = $this->data['candidates'];

        // Filtrar los candidatos para eliminar entradas vacías o con votos en cero
        $filteredCandidates = array_filter($candidatesData, function ($candidate) {
            return !empty($candidate['candidate_id']) && $candidate['votos'] > 0;
        });

        // Sincronizar la relación en la tabla pivote solo con los candidatos filtrados
        $syncData = [];
        foreach ($filteredCandidates as $candidate) {
            $syncData[$candidate['candidate_id']] = ['votos' => $candidate['votos']];
        }
        $this->record->candidates()->sync($syncData);
    }
}
