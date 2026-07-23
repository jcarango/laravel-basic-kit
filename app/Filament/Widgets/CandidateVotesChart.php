<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class CandidateVotesChart extends ChartWidget
{
    protected static ?string $heading = 'Consolidado Votos por Candidato (E-14)';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        // Calcular los votos recolectados por candidato
        $votes = DB::table('candidate_e14conteo')
            ->join('candidates', 'candidate_e14conteo.candidate_id', '=', 'candidates.id')
            ->select('candidates.name', 'candidates.lastname', DB::raw('SUM(votos) as total_votos'))
            ->groupBy('candidates.id', 'candidates.name', 'candidates.lastname')
            ->orderByDesc('total_votos')
            ->get();

        $labels = $votes->map(fn($v) => $v->name . ' ' . $v->lastname)->toArray();
        $data = $votes->map(fn($v) => (float) $v->total_votos)->toArray();

        // Generar colores semi-aleatorios basados en índice para mayor vistosidad
        $colors = [
            '#ef4444', // Red
            '#3b82f6', // Blue
            '#10b981', // Emerald
            '#f59e0b', // Amber
            '#8b5cf6', // Violet
            '#ec4899', // Pink
            '#06b6d4', // Cyan
        ];

        $backgroundColors = [];
        $colorCount = count($colors);
        for ($i = 0; $i < count($data); $i++) {
            $backgroundColors[] = $colors[$i % $colorCount];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Votos Acumulados',
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                    'borderColor' => $backgroundColors,
                    'borderWidth' => 1,
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
