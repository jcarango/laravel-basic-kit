<?php

namespace App\Filament\Widgets;

use App\Models\Suffragan;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class VotersByMunicipalityChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Participación por Municipios';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 1;


    protected function getData(): array
    {
        $data = Suffragan::query()
            ->join('cities', 'suffragans.city_id', '=', 'cities.id')
            ->select('cities.name as city_name', DB::raw('count(*) as total'))
            ->groupBy('cities.name')
            ->orderByDesc('total')
            ->limit(8)
            ->pluck('total', 'city_name')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Sufragantes',
                    'data' => array_values($data),
                    'backgroundColor' => [
                        '#6366F1', '#3B82F6', '#10B981', '#F59E0B',
                        '#EF4444', '#8B5CF6', '#EC4899', '#14B8A6',
                    ],
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
