<?php

namespace App\Filament\Resources\SuffraganResource\RelationManagers;


use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class RequirementsRelationManager extends RelationManager
{
    protected static string $relationship = 'requirements';

    protected static ?string $title = 'Requerimientos';
    protected static ?string $modelLabel = 'Requerimiento';
    protected static ?string $pluralModelLabel = 'Requerimientos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('requirement_id')
                    ->label('Requerimiento del Catálogo')
                    ->relationship('requirements', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('Estado del Requerimiento')
                    ->options([
                        'Pendiente' => 'Pendiente',
                        'En Proceso' => 'En Proceso',
                        'Resuelto' => 'Resuelto',
                    ])
                    ->default('Pendiente')
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->label('Notas / Detalle')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Requerimiento')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'particular' => 'primary',
                        'colectivo' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('pivot.status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'Resuelto' => 'success',
                        'En Proceso' => 'warning',
                        default => 'danger',
                    }),
                Tables\Columns\TextColumn::make('pivot.notes')
                    ->label('Detalles')
                    ->limit(50),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Forms\Components\Select::make('status')
                            ->label('Estado Inicial')
                            ->options([
                                'Pendiente' => 'Pendiente',
                                'En Proceso' => 'En Proceso',
                                'Resuelto' => 'Resuelto',
                            ])
                            ->default('Pendiente')
                            ->required(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Observaciones / Notas'),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
