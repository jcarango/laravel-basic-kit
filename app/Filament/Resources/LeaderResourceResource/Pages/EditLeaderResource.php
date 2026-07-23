<?php

namespace App\Filament\Resources\LeaderResourceResource\Pages;

use App\Filament\Resources\LeaderResourceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLeaderResource extends EditRecord
{
    protected static string $resource = LeaderResourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
