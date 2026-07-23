<?php

namespace App\Filament\Resources\EstrategiaResource\Pages;

use App\Filament\Resources\EstrategiaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEstrategias extends ListRecords
{
    protected static string $resource = EstrategiaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
