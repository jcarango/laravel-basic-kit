<?php

namespace App\Filament\Widgets;

use App\Models\Candidate;
use App\Models\Event;
use App\Models\FamilyMember;
use App\Models\Requirement;
use App\Models\Suffragan;
use App\Models\Survey;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ExecutiveStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalSuffragans = Suffragan::count();
        $totalOpponents = Suffragan::whereHas('candidate', fn ($q) => $q->where('is_opponent', true))->count();
        $totalLeaders = Suffragan::where('is_leader', true)->count();
        $totalEvents = Event::count();
        $totalSurveys = Survey::count();
        $totalFamilies = FamilyMember::count();
        $reqParticulares = Requirement::where('type', 'particular')->count();
        $reqColectivos = Requirement::where('type', 'colectivo')->count();

        return [
            Stat::make('Total Sufragantes', number_format($totalSuffragans))
                ->description('Registrados en sistema')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Total Opositores', number_format($totalOpponents))
                ->description('Asociados a oposición')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),

            Stat::make('Total Líderes', number_format($totalLeaders))
                ->description('Líderes de campaña')
                ->descriptionIcon('heroicon-m-star')
                ->color('success'),

            Stat::make('Total Eventos', number_format($totalEvents))
                ->description('Jornadas y reuniones')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),

            Stat::make('Total Encuestas', number_format($totalSurveys))
                ->description('Formularios parametrizados')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('warning'),

            Stat::make('Familias Registradas', number_format($totalFamilies))
                ->description('Núcleos familiares')
                ->descriptionIcon('heroicon-m-home')
                ->color('secondary'),

            Stat::make('Requerimientos Particulares', number_format($reqParticulares))
                ->description('Catálogo individual')
                ->descriptionIcon('heroicon-m-user')
                ->color('primary'),

            Stat::make('Requerimientos Colectivos', number_format($reqColectivos))
                ->description('Catálogo comunitario')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('success'),
        ];
    }
}
