<?php

namespace App\Filament\Resources\E14conteoResource\Pages;

use App\Filament\Resources\E14conteoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListE14conteos extends ListRecords
{
    protected static string $resource = E14conteoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
