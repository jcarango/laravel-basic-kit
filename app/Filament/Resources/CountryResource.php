<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CountryResource\Pages;
use App\Models\Country;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class CountryResource extends Resource
{
    protected static ?string $model = Country::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';
    protected static ?string $navigationGroup = 'Admin';
    protected static ?int $navigationSort = 2;
    protected static ?string $label = 'Paíse';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('Código ISO')
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone_code')
                    ->label('Teléfono')
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency')
                    ->label('Moneda')
                    ->sortable(),
                Tables\Columns\TextColumn::make('language')
                    ->label('Idioma')
                    ->sortable(),
                Tables\Columns\TextColumn::make('region')
                    ->label('Región')
                    ->limit(20)
                    ->tooltip(fn ($record) => $record->region),
                Tables\Columns\ImageColumn::make('flag')
                    ->label('Bandera')
                    ->circular()
                    ->height(40)
                    ->width(40),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Section::make('Información del País')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nombre')
                        ->required()
                        ->maxLength(100),

                    Forms\Components\TextInput::make('code')
                        ->label('Código ISO')
                        ->maxLength(2)
                        ->required(),

                    Forms\Components\TextInput::make('phone_code')
                        ->label('Código Teléfono')
                        ->maxLength(10),

                    Forms\Components\TextInput::make('currency')
                        ->label('Moneda')
                        ->maxLength(10),

                    Forms\Components\TextInput::make('language')
                        ->label('Idioma')
                        ->maxLength(10),

                    Forms\Components\TextInput::make('region')
                        ->label('Región')
                        ->maxLength(50),

                    Forms\Components\FileUpload::make('flag')
                        ->label('Bandera')
                        ->image()
                        ->imagePreviewHeight('100')
                        ->directory('flags')
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/svg', 'image/jpg', 'image/webp'])
                        ->maxSize(2048),
                ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCountries::route('/'),
            'create' => Pages\CreateCountry::route('/create'),
            'edit' => Pages\EditCountry::route('/{record}/edit'),
        ];
    }
}
