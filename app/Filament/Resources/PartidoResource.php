<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PartidoResource\Pages;
use App\Filament\Resources\PartidoResource\RelationManagers;
use App\Models\Partido;
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

class PartidoResource extends Resource
{
    protected static ?string $model = Partido::class;

    protected static ?string $navigationIcon = 'heroicon-s-window';
    protected static ?int $navigationSort = 8;
    protected static ?string $navigationGroup = 'Admin';
    protected static ?string $label = 'Partido';
    protected static ?string $pluralLabel = 'Partidos';


    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function form(Form $form): Form
    {
        return $form
                ->schema([
                        Section::make('Candidatos')
                            ->columns(5)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nombre')
                                    ->placeHolder('Nombre')
                                    ->hiddenLabel()
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('resolución')
                                    ->label('Resolución')
                                    ->placeHolder('Resolución')
                                    ->hiddenLabel()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('representantelegal')
                                    ->label('Representante Legal')
                                    ->placeHolder('Representante Legal')
                                    ->hiddenLabel()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->placeHolder('Email')
                                    ->hiddenLabel()
                                    ->email()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('phone')
                                    ->label('Celular')
                                    ->placeHolder('Celular')
                                    ->hiddenLabel()
                                    ->tel()
                                    ->maxLength(15),
                                Forms\Components\TextInput::make('web')
                                    ->label('Sitio WEB')
                                    ->placeHolder('Sitio WEB')
                                    ->hiddenLabel()
                                    ->maxLength(255),                        
                                Forms\Components\FileUpload::make('logo')
                                    ->preserveFilenames()
                                    ->disk('public')
                                    ->directory('partido'),
                                Forms\Components\Toggle::make('is_visible')
                                    ->label('¿Activo?')
                                    ->required(),
                            ])
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->label('Logo')
                    ->circular()
                    ->height(50)
                    ->width(50)
                    ->url(fn ($record) => url('public/' . $record->image)) // Cambia el método url()
                    ->disk('public'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('resolución')
                    ->searchable(),
                Tables\Columns\TextColumn::make('representantelegal')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('web')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_visible')
                    ->boolean(),
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
            'index' => Pages\ListPartidos::route('/'),
            'create' => Pages\CreatePartido::route('/create'),
            'edit' => Pages\EditPartido::route('/{record}/edit'),
        ];
    }
}
