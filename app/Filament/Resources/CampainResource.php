<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CampainResource\Pages;
use App\Filament\Resources\CampainResource\RelationManagers;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Models\Campain;
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

class CampainResource extends Resource
{
    protected static ?string $model = Campain::class;

    protected static ?string $navigationIcon = 'heroicon-s-paper-airplane';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'Control Electoral';
    protected static ?string $label = 'Campaña';


    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
                ->schema([
                        Section::make('Campañas')
                            ->columns(4)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nombre')
                                    ->placeHolder('Nombre')
                                    ->hiddenLabel()
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->placeHolder('Email')
                                    ->hiddenLabel()
                                    ->email()
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('phone')
                                    ->label('Celular')
                                    ->placeHolder('Celular')
                                    ->hiddenLabel()
                                    ->tel()
                                    ->required()
                                    ->maxLength(15),
                                Forms\Components\TextInput::make('web')
                                    ->label('Sito WEB')
                                    ->placeHolder('Sito WEB')
                                    ->hiddenLabel()
                                    ->required()
                                    ->maxLength(255),
                                ]),

                        Section::make('Dirección')
                            ->columns(3)
                            ->schema([
                                Forms\Components\Select::make('country_id')
                                    ->relationship(name : 'country', titleAttribute:'name')
                                    ->searchable()
                                    ->label('País')
                                    ->placeHolder('País')
                                    ->hiddenLabel()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set) {
                                        $set('state_id',null);
                                        $set('city_id',null);
                                } )
                                ->required(),
                                Forms\Components\Select::make('state_id')
                                    ->options(fn (Get $get): Collection => State::query()
                                        ->where('country_id', $get('country_id'))
                                        ->pluck('name','id'))
                                    ->searchable()
                                    ->placeHolder('Departamento')
                                    ->hiddenLabel()
                                    ->label('Departamento')
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set) =>$set('city_id',null))
                                    ->required(),
                                Forms\Components\Select::make('city_id')
                                    ->options(fn (Get $get): Collection => City::query()
                                        ->where('state_id', $get('state_id'))
                                        ->pluck('name','id'))
                                    ->searchable()
                                    ->placeHolder('Ciudad')
                                    ->hiddenLabel()
                                    ->label('Ciudad')
                                    ->preload()
                                    ->required(),
                                Forms\Components\TextInput::make('address')
                                    ->label('Dirección')
                                    ->placeHolder('Dirección')
                                    ->hiddenLabel()
                                    ->required(),
                                Forms\Components\FileUpload::make('logo')
                                    ->label('Logo')
                                    ->preserveFilenames()
                                    ->disk('public')
                                    ->hiddenLabel()
                                    ->directory('profile'),
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
                Tables\Columns\IconColumn::make('is_visible')
                    ->label('')
                    ->boolean(),
                Tables\Columns\ImageColumn::make('logo')
                    ->label('')
                    ->circular()
                    ->height(50)
                    ->width(50)
                    ->disk('public'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Correo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Celular')
                    ->searchable(),
                Tables\Columns\TextColumn::make('web')
                    ->label('Sitio WEB')
                    ->searchable(),
                Tables\Columns\TextColumn::make('country.name')
                    ->numeric()
                    ->label('País')
                    ->sortable(),
                Tables\Columns\TextColumn::make('state.name')
                    ->numeric()
                    ->label('Departamento')
                    ->sortable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->numeric()
                    ->label('Ciudad')
                    ->sortable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('Dirección')
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
            'index' => Pages\ListCampains::route('/'),
            'create' => Pages\CreateCampain::route('/create'),
            'edit' => Pages\EditCampain::route('/{record}/edit'),
        ];
    }
}
