<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Suffragan;
use Illuminate\Support\Facades\DB;

class StrategicMap extends Page
{
    public static function canAccess(): bool
    {
        return auth()->user()->hasPermissionTo('sufragantes.ver');
    }

    protected static ?int $navigationSort = 2;
    protected static ?string $navigationIcon = 'heroicon-s-map';
    protected static ?string $navigationLabel = 'Mapa Estratégico';
    protected static ?string $navigationGroup = 'Mapas';

    protected static string $view = 'filament.pages.strategic-map';


    public function getPointsByBounds($north, $south, $east, $west, $category = null)
    {
        \Illuminate\Support\Facades\Log::info("StrategicMap: Fetching points", [
            'north' => $north, 'south' => $south, 'east' => $east, 'west' => $west, 'category' => $category
        ]);

        $points = Suffragan::query()
            ->with('category')
            ->when($category && $category !== 'all', function($q) use ($category) {
                $q->whereHas('category', fn($query) => $query->where('name', $category));
            })
            // Force numeric comparison for coordinates stored as strings
            ->whereRaw('CAST(latitude AS DECIMAL(10,8)) BETWEEN ? AND ?', [$south, $north])
            ->whereRaw('CAST(longitude AS DECIMAL(11,8)) BETWEEN ? AND ?', [$west, $east])
            ->limit(4000)
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'name' => $s->name,
                    'lastname' => $s->lastname,
                    'latitude' => $s->latitude,
                    'longitude' => $s->longitude,
                    'documentationnumber' => $s->documentationnumber,
                    'email' => $s->email,
                    'phone' => $s->phone,
                    'photo' => $s->photo,
                    'category' => $s->category?->name ?? 'Militantes',
                    'color' => match($s->category?->name) {
                        'Líderes' => 'red',
                        'Nodo Multiplicador' => 'purple',
                        'Testigo Electoral' => 'yellow',
                        default => 'green'
                    }
                ];
            });

        \Illuminate\Support\Facades\Log::info("StrategicMap: Found points: " . $points->count());

        return $points;
    }

    public function getComunaCounts()
    {
        if (!\Illuminate\Support\Facades\Schema::hasColumn('suffragans', 'comuna')) {
            return [];
        }

        return Suffragan::select(
                'comuna',
                DB::raw('count(*) as total')
            )
            ->whereNotNull('comuna')
            ->groupBy('comuna')
            ->pluck('total','comuna');
    }
}