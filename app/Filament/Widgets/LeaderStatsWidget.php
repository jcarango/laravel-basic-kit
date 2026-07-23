<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LeaderStatsWidget extends BaseWidget
{
    public static function canView(): bool
    {
        return auth()->user()->hasRole('lider');
    }

    protected function getStats(): array
    {
        $user = auth()->user();
        $count = \App\Models\Suffragan::where('user_id', $user->id)->count();
        $goal = $user->monthly_goal ?? 50;
        
        $percentage = $goal > 0 ? min(100, round(($count / $goal) * 100)) : 100;
        $remaining = max(0, $goal - $count);

        return [
            Stat::make('Mis Sufragantes', $count)
                ->description("Meta: {$goal} | Faltan: {$remaining}")
                ->descriptionIcon('heroicon-m-target')
                ->color($percentage >= 100 ? 'success' : ($percentage >= 50 ? 'warning' : 'danger')),
            
            Stat::make('Progreso de Meta', "{$percentage}%")
                ->description('Avance del mes')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([min(20, $percentage), min(50, $percentage), min(80, $percentage), $percentage])
                ->color('info'),
        ];
    }
}
