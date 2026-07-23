<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ObservationResource\Pages;
use App\Filament\Resources\ObservationResource\RelationManagers;
use App\Models\Observation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\EnumColumn;
use Filament\Forms\Components\Section;
use Illuminate\Support\Collection;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;

class ObservationResource extends Resource
{
    protected static ?string $model = Observation::class;

    protected static ?string $navigationIcon = 'heroicon-s-eye';
    protected static ?int $navigationSort = 14;
    protected static ?string $navigationGroup = 'Control Electoral';
    protected static ?string $label = 'Observaciones';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
                ->schema([
                        Section::make('Observaciones')
                            ->columns(3)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nombre')
                                    ->placeHolder('Nombre')
                                    ->hiddenLabel()
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('input')
                                    ->required()
                                    ->label('Monto')
                                    ->placeHolder('Monto')
                                    ->hiddenLabel()
                                    ->maxLength(255),
                                Forms\Components\Select::make('suffragan_id')
                                    ->relationship('suffragan', 'name')
                                    ->hiddenLabel()
                                    ->label('Sufragante')
                                    ->preload()
                                    ->searchable()
                                    ->placeholder('Selecciona la Persona')
                                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} {$record->lastname}"),
                                Forms\Components\RichEditor::make('description')
                                    ->columnSpan(2)
                                    ->placeHolder('Descripcion del aporte')
                                    ->hiddenLabel()
                                    ->label('Descripcion del aporte')
                                    ->columnSpanFull(),
                            ])
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([ 
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('input')
                    ->label('Monto')
                    ->formatStateUsing(fn ($state) => number_format($state, 0, '.', ','))
                    ->searchable(),
                Tables\Columns\TagsColumn::make('suffragan')
                    ->label('Sufragante')
                    ->label('Sufragante')
                    ->formatStateUsing(function ($state, $record) {
                        return $record->suffragan ? $record->suffragan->name . ' ' . $record->suffragan->lastname : 'Sin asignar';
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Descripción')
                    ->formatStateUsing(fn($state) => strip_tags($state))
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListObservations::route('/'),
            'create' => Pages\CreateObservation::route('/create'),
            'edit' => Pages\EditObservation::route('/{record}/edit'),
        ];
    }
}
