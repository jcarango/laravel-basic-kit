<?php

namespace App\Filament\Resources\JjjjResource\Widgets;

use Filament\Widgets\ChartWidget;

class HeatmapWidget extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected function getData(): array
    {
        return [
            //
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
