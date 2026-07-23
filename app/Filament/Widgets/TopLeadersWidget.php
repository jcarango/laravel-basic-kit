<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TopLeadersWidget extends BaseWidget
{
    protected static ?string $heading = '🏆 Top Líderes (Gamificación)';
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 1;


    public static function canView(): bool
    {
        return auth()->user()->hasRole(['super-admin', 'coordinador']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                \App\Models\User::query()
                    ->withCount('suffragans')
                    ->orderByDesc('suffragans_count')
                    ->having('suffragans_count', '>', 0)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre'),
                Tables\Columns\TextColumn::make('lastname')
                    ->label('Apellido'),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Cargo')
                    ->badge(),
                Tables\Columns\TextColumn::make('suffragans_count')
                    ->label('Sufragantes Registrados')
                    ->badge()
                    ->color('success')
                    ->sortable(),
            ]);
    }
}
