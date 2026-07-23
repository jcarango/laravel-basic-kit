<?php

namespace App\Filament\Resources\SuffraganResource\Pages;

use App\Filament\Resources\SuffraganResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Http;

class CreateSuffragan extends CreateRecord
{
    protected static string $resource = SuffraganResource::class;

    protected function afterSave(): void
    {
        // Accede al registro recién creado
        $record = $this->record;

        if ($record->address) {
            // Llamada a la API para obtener las coordenadas
            $geoResponse = Http::get("https://nominatim.openstreetmap.org/search", [
                'q' => $record->address,
                'format' => 'json',
                'addressdetails' => 1,
                'limit' => 1,
            ]);

            if ($geoResponse->successful() && $geoResponse->json()) {
                $geoData = $geoResponse->json()[0];

                // Actualiza el registro con latitud y longitud
                $record->update([
                    'latitude' => $geoData['lat'],
                    'longitude' => $geoData['lon'],
                ]);
            }
        }

        $record->calculateProfileScore();
    }
}
