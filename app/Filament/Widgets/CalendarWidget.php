<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use App\Models\Event;
use App\Filament\Resources\EventResource;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\ColorPicker;
use App\Filament\Widgets\StageAlertsChart;

class CalendarWidget extends FullCalendarWidget
{

    public function dashboard(): array
    {
        return [
            StageAlertsChart::class,
        ];
    }
    
    public Model | string | null $model = Event::class;

    public function fetchEvents(array $fetchInfo): array
    {
        return Event::query()
            ->where('starts_at', '>=', $fetchInfo['start'])
            ->where('ends_at', '<=', $fetchInfo['end'])
            ->get()
            ->map(
                fn (Event $event) => [
                    'id' => $event->id,
                    'name' => $event->name,
                    'color' => $event->color,
                    'start' => $event->starts_at,
                    'end' => $event->ends_at,
                ]
            )
            ->all();
    }

    public function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->label('Nombre')
                ->required(),
            Forms\Components\TextInput::make('description')
                ->label('Descripción')
                ->required(),
            Forms\Components\ColorPicker::make('color')
                ->label('Selecciona un color')
                ->required(),
            Forms\Components\Grid::make()
                ->schema([
                    Forms\Components\DateTimePicker::make('starts_at')
                        ->required(),
 
                    Forms\Components\DateTimePicker::make('ends_at')
                        ->required(),
                ]),
        ];
    }
}
