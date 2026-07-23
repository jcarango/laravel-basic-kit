<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DivipolResource\Pages;
use App\Filament\Resources\DivipolResource\RelationManagers;
use App\Models\Divipol;
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

class DivipolResource extends Resource
{
    protected static ?string $model = Divipol::class;

    protected static ?string $navigationIcon = 'heroicon-s-window';
    protected static ?int $navigationSort = 7;
    protected static ?string $navigationGroup = 'Admin';
    protected static ?string $label = 'División Políticas';
    protected static ?string $pluralLabel = 'División Políticas';


    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
                ->schema([
                        Section::make('Divipol')
                            ->columns(4)
                            ->schema([
                                Forms\Components\TextInput::make('dep')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('mun')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('zon')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('pto')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('departamento')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('municipio')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('nom_puesto')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('direccion')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('ind_mesa')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('categoria')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('mujeres')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('hombres')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('potencial')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('mesas_totales')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('jal')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('nom_jal')
                                    ->required()
                                    ->maxLength(255),
                            ])
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('dep')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mun')
                    ->searchable(),
                Tables\Columns\TextColumn::make('zon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pto')
                    ->searchable(),
                Tables\Columns\TextColumn::make('departamento')
                    ->searchable(),
                Tables\Columns\TextColumn::make('municipio')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nom_puesto')
                    ->searchable(),
                Tables\Columns\TextColumn::make('direccion')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ind_mesa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('categoria')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mujeres')
                    ->searchable(),
                Tables\Columns\TextColumn::make('hombres')
                    ->searchable(),
                Tables\Columns\TextColumn::make('potencial')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mesas_totales')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jal')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nom_jal')
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
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListDivipols::route('/'),
            'create' => Pages\CreateDivipol::route('/create'),
            'edit' => Pages\EditDivipol::route('/{record}/edit'),
        ];
    }
}
