<?php

namespace App\Filament\Resources\SuffraganResource\Pages;

use App\Filament\Resources\SuffraganResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Http;

class EditSuffragan extends EditRecord
{
    protected static string $resource = SuffraganResource::class;

    protected function afterSave(): void
    {
        $record = $this->record;

        if ($record->isDirty('address')) {
            // Repetir la lógica para actualizar coordenadas
            $geoResponse = Http::get("https://nominatim.openstreetmap.org/search", [
                'q' => $record->address,
                'format' => 'json',
                'addressdetails' => 1,
                'limit' => 1,
            ]);

            if ($geoResponse->successful() && $geoResponse->json()) {
                $geoData = $geoResponse->json()[0];
                $record->update([
                    'latitude' => $geoData['lat'],
                    'longitude' => $geoData['lon'],
                ]);
            }
        }

        $record->calculateProfileScore();
    }
}
