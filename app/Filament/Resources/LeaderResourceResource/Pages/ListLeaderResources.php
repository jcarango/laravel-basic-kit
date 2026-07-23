<?php

namespace App\Filament\Resources\LeaderResourceResource\Pages;

use App\Filament\Resources\LeaderResourceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLeaderResources extends ListRecords
{
    protected static string $resource = LeaderResourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\LeaderResourceResource\Widgets\LeaderResourceOverview::class,
        ];
    }
}
