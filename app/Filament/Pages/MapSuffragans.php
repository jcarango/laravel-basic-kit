<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

use Swapinvidya\HuggingFaceClient\HuggingFaceClient;

class MapSuffragans extends Page
{
    public static function canAccess(): bool
    {
        return auth()->user()->hasPermissionTo('sufragantes.ver');
    }

    protected static ?int $navigationSort = 1;
    protected static ?string $navigationIcon = 'heroicon-s-map';
    protected static ?string $navigationLabel = 'Mapa de Sufragantes';
    protected static ?string $navigationGroup = 'Mapas';
    protected static ?string $label = 'Mapa de Sufragantes';


    protected static string $view = 'filament.pages.map-suffragans';

    public function getPoints()
    {
        return DB::table('suffragans')
            ->leftJoin('categories', 'suffragans.categories_id', '=', 'categories.id') 
            ->select(
                'suffragans.name',
                'suffragans.lastname',
                'suffragans.latitude',
                'suffragans.longitude',
                'suffragans.documentationnumber',
                'suffragans.email',
                'suffragans.phone',
                'suffragans.photo',
                'categories.name as category'
            )
            ->whereNotNull('suffragans.latitude')
            ->whereNotNull('suffragans.longitude')
            ->get()
            ->map(function ($point) {
                $categoryName = strtolower($point->category ?? 'militante');
                $point->color = match ($categoryName) {
                    'nodo multiplicador' => 'purple',
                    'lider' => 'red', 
                    'testigo electoral' => 'yellow',
                    'militante' => 'green',
                    default => 'blue',
                };
                $point->category = $point->category ?? 'Militante';
                return $point;
            });
    }

    public function generateImage(HuggingFaceClient $huggingFaceClient)
    {
        $response = $huggingFaceClient->generateImage('stable-diffusion-v1', 'A futuristic city with flying cars.');
        return response()->json($response);
    }

    public function generateText(HuggingFaceClient $huggingFaceClient)
    {
        $response = $huggingFaceClient->generateText('gpt2', 'Write a short story about a hero.');
        return response()->json($response);
    }

}
