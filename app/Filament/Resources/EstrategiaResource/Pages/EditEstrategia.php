<?php

namespace App\Filament\Resources\EstrategiaResource\Pages;

use App\Filament\Resources\EstrategiaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Jobs\ProcessEstrategiaAnalysis;

class EditEstrategia extends EditRecord
{
    protected static string $resource = EstrategiaResource::class;

    protected function afterSave(): void
    {
        $camposRelevantes = [
            'quiereser', 'determinoimagen', 'identificoproblemas',
            'identificoseguidores', 'identificocapacidad', 'iteresproyecto',
            'mejorqueotros', 'Propuesta', 'sectorpriorizado', 'problematicadeterminada',
            'objetivogeneral', 'objetivosestrategicos', 'planeacionestrategia',
            'plandesarrollo', 'planproceso', 'planmejoramiento', 'situacionreal',
            'insumos', 'procesos', 'productos', 'resultados', 'impactos', 'situacionlograble'
        ];
        
        if ($this->record->wasChanged($camposRelevantes)) {
            ProcessEstrategiaAnalysis::dispatch($this->record);
        }
    }
}