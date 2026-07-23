<?php

namespace App\Filament\Resources\DivipolResource\Pages;

use App\Filament\Resources\DivipolResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDivipol extends EditRecord
{
    protected static string $resource = DivipolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
