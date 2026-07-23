<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Filament\Resources\UserResource\Pages;
use Filament\Forms\Components\Section;
use Illuminate\Support\Collection;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'Admin';
    protected static ?int $navigationSort = 1;
    protected static ?string $label = 'Usuarios';


    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            // Sección 1: Información Personal
            Forms\Components\Section::make('Información Personal')
                ->columns(3)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nombre')
                        ->required(),
                    Forms\Components\TextInput::make('lastname')
                        ->label('Apellido'),
                    Forms\Components\TextInput::make('email')
                        ->label('Correo electrónico')
                        ->required()
                        ->email()
                        ->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('phone')
                        ->label('Teléfono'),
                    Forms\Components\TextInput::make('password')
                        ->label('Contraseña')
                        ->password()
                        ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                        ->dehydrated(fn ($state) => filled($state))
                        ->hiddenOn('edit'),
                ]),

            // Sección 2: Dirección
            Forms\Components\Section::make('Dirección')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('address')
                        ->label('Dirección'),
                    Forms\Components\Select::make('country_id')
                        ->label('País')
                        ->relationship('country', 'name')
                        ->searchable()
                        ->preload(),
                    Forms\Components\Select::make('state_id')
                        ->label('Departamento')
                        ->relationship('state', 'name')
                        ->searchable()
                        ->preload(),
                    Forms\Components\Select::make('city_id')
                        ->label('Ciudad')
                        ->relationship('city', 'name')
                        ->searchable()
                        ->preload(),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Activo')
                        ->default(true),
                    Forms\Components\Toggle::make('habeas_data_accepted')
                        ->label('Acepta Habeas Data')
                        ->default(false),
                    Forms\Components\FileUpload::make('avatar')
                        ->label('Avatar')
                        ->image()
                        ->imagePreviewHeight('100')
                        ->directory('avatars')
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/webp'])
                        ->maxSize(2048),
                ]),

            // Sección 3: Roles y Permisos
            Forms\Components\Section::make('Roles y Permisos')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('roles')
                        ->label('Roles')
                        ->relationship('roles', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable(),
                    Forms\Components\Select::make('permissions')
                        ->label('Permisos directos')
                        ->relationship('permissions', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable(),
                    Forms\Components\TextInput::make('monthly_goal')
                        ->label('Meta Mensual (Sufragantes)')
                        ->numeric()
                        ->default(50)
                        ->helperText('Solo aplica para usuarios con rol de Líder.'),
                ]),
        ]);
}

            

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id')->sortable(),
            Tables\Columns\IconColumn::make('is_active')
                ->boolean()
                ->label('Activo'),
            Tables\Columns\ImageColumn::make('avatar')
                ->label('Avatar')
                ->circular()
                ->height(40)
                ->width(40),
            Tables\Columns\TextColumn::make('name')->searchable(),
            Tables\Columns\TextColumn::make('lastname')->searchable(),
            Tables\Columns\TextColumn::make('phone')->label('Teléfono'),
            Tables\Columns\TextColumn::make('address')->label('Dirección'),
            Tables\Columns\TextColumn::make('country.name')->label('País'),
            Tables\Columns\TextColumn::make('state.name')->label('Estado'),
            Tables\Columns\TextColumn::make('city.name')->label('Ciudad'),
            Tables\Columns\TextColumn::make('email')->searchable(),
            Tables\Columns\TextColumn::make('created_at')->dateTime(),
        ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/crear'),
            'edit' => Pages\EditUser::route('/{record}/editar'),
        ];
    }
}
