<?php

namespace App\Filament\Resources\SuffragantDivipolResource\Pages;

use App\Filament\Resources\SuffragantDivipolResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSuffragantDivipols extends ListRecords
{
    protected static string $resource = SuffragantDivipolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
