<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UpcomingEventsWidget extends BaseWidget
{
    protected static ?string $heading = '📅 Próximos Eventos de Campaña';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Event::query()
                    ->orderBy('starts_at', 'desc')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Evento')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('starts_at')
                    ->label('Fecha / Hora')
                    ->dateTime('d/m/Y g:i A')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stage')
                    ->label('Etapa')
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        'General' => 'info',
                        'Planeación' => 'warning',
                        'Avanzada' => 'primary',
                        'Durante el evento' => 'success',
                        'Después del evento' => 'gray',
                        default => 'info',
                    }),
                Tables\Columns\TextColumn::make('city.name')
                    ->label('Municipio'),
            ])
            ->paginated(false);
    }
}
