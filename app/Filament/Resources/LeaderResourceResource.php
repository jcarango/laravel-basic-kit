<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaderResourceResource\Pages;
use App\Models\LeaderResource;
use App\Models\Suffragan;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LeaderResourceResource extends Resource
{
    protected static ?string $model = LeaderResource::class;

    protected static ?string $navigationIcon = 'heroicon-s-gift';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Eventos de Campaña';
    protected static ?string $modelLabel = 'Recurso de Líder';
    protected static ?string $pluralModelLabel = 'Recursos De Líderes';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(3)
                    ->schema([
                        Section::make('Asignación de Recurso')
                            ->description('Detalles principales del recurso y el líder beneficiario.')
                            ->columnSpan(2)
                            ->columns(2)
                            ->schema([
                                Forms\Components\Select::make('leader_id')
                                    ->label('Líder Beneficiario')
                                    ->relationship(
                                        name: 'leader',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn (Builder $query) => $query->where('is_leader', true)
                                    )
                                    ->getOptionLabelFromRecordUsing(fn (Suffragan $record) => "{$record->name} {$record->lastname} - CC: {$record->documentationnumber}")
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Forms\Components\Select::make('type')
                                    ->label('Tipo de Recurso')
                                    ->options([
                                        'Especie' => 'En Especie (Materiales/Merchandising)',
                                        'Dinero' => 'Dinero / Apoyo Financiero',
                                        'Contrato' => 'Contrato / Promesa Laboral',
                                        'Obra' => 'Obra / Gestión Comunitaria',
                                        'Ayuda' => 'Ayuda Social / Humanitaria',
                                        'Bono' => 'Bonos / Vales de Gasolina/Comida',
                                        'Otro' => 'Otro',
                                    ])
                                    ->required(),

                                Forms\Components\TextInput::make('concept')
                                    ->label('Concepto / Descripción Corta')
                                    ->placeholder('Ej. Kit de 50 camisetas, Apoyo transporte D-Día')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('quantity')
                                    ->label('Cantidad')
                                    ->required()
                                    ->numeric()
                                    ->default(1),

                                Forms\Components\TextInput::make('value')
                                    ->label('Valor / Presupuesto Asignado')
                                    ->numeric()
                                    ->prefix('$')
                                    ->placeholder('Ej. 500000'),

                                Forms\Components\Select::make('status')
                                    ->label('Estado de Gestión')
                                    ->options([
                                        'Solicitado' => 'Solicitado',
                                        'Aprobado' => 'Aprobado',
                                        'En Proceso' => 'En Proceso / Camino',
                                        'Entregado' => 'Entregado / Ejecutado',
                                        'Rechazado' => 'Rechazado',
                                    ])
                                    ->required()
                                    ->default('Solicitado'),

                                Forms\Components\DatePicker::make('delivery_date')
                                    ->label('Fecha de Entrega / Ejecución'),
                            ]),

                        Grid::make(1)
                            ->columnSpan(1)
                            ->schema([
                                Section::make('Soporte y Gestión')
                                    ->description('Documentación y usuario responsable.')
                                    ->schema([
                                        Forms\Components\FileUpload::make('attachment_path')
                                            ->label('Soporte / Evidencia')
                                            ->disk('public')
                                            ->directory('leader_resources')
                                            ->preserveFilenames()
                                            ->maxSize(5120) // 5MB
                                            ->helperText('PDF, Imágenes o documentos firmados (Máx. 5MB)'),

                                        Forms\Components\Select::make('user_id')
                                            ->label('Gestor del Sistema')
                                            ->relationship('user', 'name')
                                            ->default(fn () => auth()->id())
                                            ->disabled()
                                            ->dehydrated(),

                                        Forms\Components\TextInput::make('responsible_person')
                                            ->label('Responsable Externo')
                                            ->placeholder('Ej. Juan Perez (Coordinador)')
                                            ->maxLength(255),
                                    ]),
                            ]),

                        Section::make('Información Detallada')
                            ->columnSpanFull()
                            ->schema([
                                Forms\Components\Textarea::make('description')
                                    ->label('Descripción / Alcance del Recurso')
                                    ->placeholder('Detalles adicionales sobre el recurso, tallas, propósitos...')
                                    ->rows(3)
                                    ->columnSpanFull(),

                                Forms\Components\Textarea::make('observations')
                                    ->label('Observaciones / Novedades')
                                    ->placeholder('Comentarios sobre la entrega, retrasos o novedades...')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('leader')
                    ->label('Líder Beneficiario')
                    ->state(fn (LeaderResource $record) => "{$record->leader?->name} {$record->leader?->lastname}")
                    ->description(fn (LeaderResource $record) => "CC: {$record->leader?->documentationnumber}")
                    ->searchable(query: fn (Builder $query, string $search) => 
                        $query->whereHas('leader', fn ($q) => 
                            $q->where('name', 'like', "%{$search}%")
                              ->orWhere('lastname', 'like', "%{$search}%")
                              ->orWhere('documentationnumber', 'like', "%{$search}%")
                        )
                    )
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Especie' => 'info',
                        'Dinero' => 'success',
                        'Contrato' => 'warning',
                        'Obra' => 'danger',
                        'Ayuda' => 'primary',
                        'Bono' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('concept')
                    ->label('Concepto')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Cant.')
                    ->numeric()
                    ->alignCenter()
                    ->sortable(),

                Tables\Columns\TextColumn::make('value')
                    ->label('Valor Asignado')
                    ->money('COP', locale: 'es_CO')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Solicitado' => 'gray',
                        'Aprobado' => 'info',
                        'En Proceso' => 'warning',
                        'Entregado' => 'success',
                        'Rechazado' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('delivery_date')
                    ->label('Fecha Entrega')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\IconColumn::make('attachment_path')
                    ->label('Soporte')
                    ->boolean()
                    ->state(fn (LeaderResource $record) => !empty($record->attachment_path))
                    ->trueIcon('heroicon-o-document-check')
                    ->falseIcon('heroicon-o-document-minus')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo de Recurso')
                    ->options([
                        'Especie' => 'En Especie',
                        'Dinero' => 'Dinero / Efectivo',
                        'Contrato' => 'Contrato',
                        'Obra' => 'Obra / Gestión',
                        'Ayuda' => 'Ayuda Social',
                        'Bono' => 'Bono / Vale',
                        'Otro' => 'Otro',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'Solicitado' => 'Solicitado',
                        'Aprobado' => 'Aprobado',
                        'En Proceso' => 'En Proceso',
                        'Entregado' => 'Entregado',
                        'Rechazado' => 'Rechazado',
                    ]),

                Tables\Filters\SelectFilter::make('leader_id')
                    ->label('Líder')
                    ->relationship('leader', 'name', modifyQueryUsing: fn ($query) => $query->where('is_leader', true))
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('download_attachment')
                        ->label('Descargar Soporte')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->url(fn (LeaderResource $record) => $record->attachment_path ? asset('storage/' . $record->attachment_path) : null)
                        ->openUrlInNewTab()
                        ->visible(fn (LeaderResource $record) => !empty($record->attachment_path)),
                    Tables\Actions\DeleteAction::make(),
                ])
                ->label('Acciones')
                ->icon('heroicon-m-bars-3')
                ->tooltip('Mostrar acciones')
            ], position: \Filament\Tables\Enums\ActionsPosition::BeforeColumns)
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

    public static function getWidgets(): array
    {
        return [
            \App\Filament\Resources\LeaderResourceResource\Widgets\LeaderResourceOverview::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeaderResources::route('/'),
            'create' => Pages\CreateLeaderResource::route('/create'),
            'edit' => Pages\EditLeaderResource::route('/{record}/edit'),
        ];
    }
}

