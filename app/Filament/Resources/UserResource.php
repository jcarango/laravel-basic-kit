<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Forms;
use Filament\Resources\Resource;
use App\Filament\Resources\UserResource\Pages;
use Filament\Tables;
use Illuminate\Support\Facades\Hash;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\Select;
use Spatie\Permission\Models\Role;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Illuminate\Support\Collection;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Blade;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'Admin';
    protected static ?int $navigationSort = 1;
    protected static ?string $label = 'Usuario';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

public static function form(Forms\Form $form): Forms\Form
{
    return $form->schema([
        Section::make('Información Personal')
            ->columns(4)
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('lastname')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->label('Teléfono')
                    ->maxLength(20),
                Forms\Components\TextInput::make('email')
                    ->required()
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
            ]),
        Section::make('Dirección')
            ->columns(2)
            ->schema([
                Forms\Components\TextInput::make('address')
                    ->label('Dirección')
                    ->maxLength(255),
                Forms\Components\Select::make('country_id')
                    ->label('País')
                    ->options(\App\Models\Country::all()->pluck('name', 'id'))
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set) => $set('state_id', null)),
                Forms\Components\Select::make('state_id')
                    ->label('Estado')
                    ->options(fn (callable $get) =>
                        \App\Models\State::where('country_id', $get('country_id'))->pluck('name', 'id')
                    )
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set) => $set('city_id', null)),
                Forms\Components\Select::make('city_id')
                    ->label('Ciudad')
                    ->options(fn (callable $get) =>
                        \App\Models\City::where('state_id', $get('state_id'))->pluck('name', 'id')
                    ),
            ]),
        Section::make('Otros')
            ->columns(3)
            ->schema([

                Forms\Components\Toggle::make('is_active')
                    ->label('Activo'),
                Forms\Components\FileUpload::make('avatar')
                    ->label('Avatar')
                    ->image()
                    ->imagePreviewHeight('100')
                    ->directory('avatars')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/webp'])
                    ->maxSize(2048),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->label('Contraseña')
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                    ->required(fn (string $context) => $context === 'create')
                    ->hidden(fn (string $context) => $context === 'edit'),
                Forms\Components\CheckboxList::make('roles')
                    ->label('Roles')
                    ->relationship('roles', 'name')
                    ->columns(2),
            ]),
    ]);
}


    public static function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([
            Tables\Columns\IconColumn::make('is_active')
                ->boolean()
                ->label('Activo'),
            Tables\Columns\ImageColumn::make('avatar')
                ->label('Avatar')
                ->circular()
                ->height(40)
                ->width(40),
            Tables\Columns\TextColumn::make('id')->sortable(),
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
                    Tables\Actions\BulkAction::make('Export')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->openUrlInNewTab()
                    ->deselectRecordsAfterCompletion()
                    ->action(function (Collection $records) {
                        return response()->streamDownload(function () use ($records) {
                            echo Pdf::loadHTML(
                                Blade::render('users', ['records' => $records])
                            )->stream();
                        }, 'users.pdf');
                    }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
