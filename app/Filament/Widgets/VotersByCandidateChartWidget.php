<?php

namespace App\Filament\Widgets;

use App\Models\Candidate;
use Filament\Widgets\ChartWidget;

class VotersByCandidateChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Participación por Candidato';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $candidates = Candidate::withCount('suffragans')->get();

        $labels = $candidates->map(fn ($c) => "{$c->name} {$c->lastname}")->toArray();
        $counts = $candidates->pluck('suffragans_count')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Sufragantes Asociados',
                    'data' => $counts,
                    'backgroundColor' => '#3B82F6',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
