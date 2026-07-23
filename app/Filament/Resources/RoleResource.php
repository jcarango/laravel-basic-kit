<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Spatie\Permission\Models\Role;
use Filament\Forms\Form;
use Filament\Tables\Table;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationGroup = 'Admin';
    protected static ?int $navigationSort = 71;
    protected static ?string $label = 'Roles';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nombre del Rol')
                ->required(),

            Forms\Components\Select::make('permissions')
                ->label('Permisos')
                ->relationship('permissions', 'name')
                ->multiple()
                ->preload()
                ->searchable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Rol'),
                Tables\Columns\TextColumn::make('permissions.name')->label('Permisos')->badge(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/crear'),
            'edit' => Pages\EditRole::route('/{record}/editar'),
        ];
    }
}
