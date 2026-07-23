<?php

namespace App\Filament\Resources\EstrategiaResource\Pages;

use App\Filament\Resources\EstrategiaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Jobs\ProcessEstrategiaAnalysis;

class CreateEstrategia extends CreateRecord
{
    protected static string $resource = EstrategiaResource::class;

    protected function afterCreate(): void
    {
        ProcessEstrategiaAnalysis::dispatch($this->record);
    }
}