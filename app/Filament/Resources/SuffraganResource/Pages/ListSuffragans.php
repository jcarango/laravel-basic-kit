<?php

namespace App\Filament\Resources\SuffraganResource\Pages;

use App\Filament\Resources\SuffraganResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSuffragans extends ListRecords
{
    protected static string $resource = SuffraganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
