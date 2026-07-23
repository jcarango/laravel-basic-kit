<?php

namespace App\Filament\Resources\CampainResource\Pages;

use App\Filament\Resources\CampainResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCampain extends EditRecord
{
    protected static string $resource = CampainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
