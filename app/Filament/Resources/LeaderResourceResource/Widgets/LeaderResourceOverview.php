<?php

namespace App\Filament\Resources\LeaderResourceResource\Widgets;

use App\Models\LeaderResource;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class LeaderResourceOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalBudget = LeaderResource::sum('value') ?? 0;
        $totalCount = LeaderResource::count();
        $deliveredCount = LeaderResource::where('status', 'Entregado')->count();
        $pendingCount = LeaderResource::whereIn('status', ['Solicitado', 'Aprobado', 'En Proceso'])->count();
        
        $mostCommonTypeRecord = LeaderResource::select('type', DB::raw('count(*) as total'))
            ->groupBy('type')
            ->orderBy('total', 'desc')
            ->first();
            
        $mostCommonType = $mostCommonTypeRecord ? $mostCommonTypeRecord->type : 'Ninguno';
        $mostCommonCount = $mostCommonTypeRecord ? $mostCommonTypeRecord->total : 0;

        return [
            Stat::make('Presupuesto Total Asignado', '$ ' . number_format($totalBudget, 0, ',', '.'))
                ->description('Inversión total acumulada en líderes')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
                
            Stat::make('Recursos Entregados / Ejecutados', "{$deliveredCount} de {$totalCount}")
                ->description("{$pendingCount} recurso(s) pendiente(s) de entrega")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($deliveredCount === $totalCount && $totalCount > 0 ? 'success' : 'warning'),
                
            Stat::make('Recurso Más Asignado', $mostCommonType)
                ->description("{$mostCommonCount} asignación(es) registrada(s)")
                ->descriptionIcon('heroicon-m-gift')
                ->color('info'),
        ];
    }
}

