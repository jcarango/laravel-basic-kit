<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CandidateResource\Pages;
use App\Filament\Resources\CandidateResource\RelationManagers;
use App\Models\Candidate;
use App\Models\Partido;
use App\Models\Campain;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
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
use Swapinvidya\LaravelHuggingFaceClient\Facades\HuggingFace;
use Filament\Forms\Components\BelongsToManyRelationManager;
use Filament\Forms\Components\MultiSelect;

class CandidateResource extends Resource
{
    protected static ?string $model = Candidate::class;

    protected static ?string $navigationIcon = 'heroicon-s-user-circle';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Control Electoral';
    protected static ?string $label = 'Candidato';


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
                                Forms\Components\TextInput::make('lastname')
                                    ->label('Apellido')
                                    ->placeHolder('Apellido')
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
                                    ->label('Sitio WEB')
                                    ->placeHolder('Sitio WEB')
                                    ->hiddenLabel()
                                    ->required()
                                    ->maxLength(255),
                            ]),

                        Section::make('Dirección')
                            ->columns(4)
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
                                Forms\Components\FileUpload::make('photo')
                                    ->label('Fotografía')
                                    ->image()
                                    ->disk('public')
                                    ->directory('profile')
                                    ->visibility('public')
                                    ->preserveFilenames(),

                                Forms\Components\Select::make('campains')
                                    ->relationship('campains', 'name')
                                    ->preload()
                                    ->hiddenLabel()
                                    ->searchable()
                                    ->label('Campaña')
                                    ->placeholder('Selecciona Campaña'),
                                Forms\Components\Select::make('partido_id')
                                    ->label('Partido')
                                    ->relationship('partido', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->placeholder('Selecciona Partido'),
                                Forms\Components\Select::make('cargo_aspira')
                                    ->label('Cargo al que aspira')
                                    ->options([
                                        'Alcaldía' => 'Alcaldía',
                                        'Concejo' => 'Concejo',
                                        'Asamblea' => 'Asamblea',
                                        'Gobernación' => 'Gobernación',
                                        'Cámara' => 'Cámara',
                                        'Senado' => 'Senado',
                                    ])
                                    ->required()
                                    ->placeholder('Selecciona Cargo'),
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Activo')
                                    ->default(true),
                                Forms\Components\Toggle::make('is_principal')
                                    ->label('Principal')
                                    ->default(false),
                                Forms\Components\Toggle::make('is_opponent')
                                    ->label('Opositor (Sí/No)')
                                    ->default(false),
                            ])
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_opponent')
                    ->label('Opositor')
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success'),
                Tables\Columns\ImageColumn::make('photo')
                    ->label('Foto')
                    ->circular()
                    ->height(50)
                    ->width(50)
                    ->disk('public'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('lastname')
                    ->label('Apellido')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cargo_aspira')
                    ->label('Cargo')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('partido.name')
                    ->label('Partido')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Celular')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->label('Ciudad')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_opponent')
                    ->label('Ver Opositores'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Ver Activos'),
                Tables\Filters\SelectFilter::make('cargo_aspira')
                    ->label('Cargo Aspirado')
                    ->options([
                        'Alcaldía' => 'Alcaldía',
                        'Concejo' => 'Concejo',
                        'Asamblea' => 'Asamblea',
                        'Gobernación' => 'Gobernación',
                        'Cámara' => 'Cámara',
                        'Senado' => 'Senado',
                    ]),
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
            'index' => Pages\ListCandidates::route('/'),
            'create' => Pages\CreateCandidate::route('/create'),
            'edit' => Pages\EditCandidate::route('/{record}/edit'),
        ];
    }
}
