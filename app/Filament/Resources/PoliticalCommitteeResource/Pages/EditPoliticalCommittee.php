<?php

namespace App\Filament\Resources\PoliticalCommitteeResource\Pages;

use App\Filament\Resources\PoliticalCommitteeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPoliticalCommittee extends EditRecord
{
    protected static string $resource = PoliticalCommitteeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
