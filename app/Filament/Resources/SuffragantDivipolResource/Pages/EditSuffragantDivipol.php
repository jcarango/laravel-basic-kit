<?php

namespace App\Filament\Resources\SuffragantDivipolResource\Pages;

use App\Filament\Resources\SuffragantDivipolResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSuffragantDivipol extends EditRecord
{
    protected static string $resource = SuffragantDivipolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
