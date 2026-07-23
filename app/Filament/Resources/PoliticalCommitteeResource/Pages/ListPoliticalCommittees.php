<?php

namespace App\Filament\Resources\PoliticalCommitteeResource\Pages;

use App\Filament\Resources\PoliticalCommitteeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPoliticalCommittees extends ListRecords
{
    protected static string $resource = PoliticalCommitteeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
