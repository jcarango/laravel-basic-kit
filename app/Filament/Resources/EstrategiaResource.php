<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EstrategiaResource\Pages;
use App\Filament\Resources\EstrategiaResource\RelationManagers;
use App\Models\Estrategia;
use App\Models\Candidate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables; 
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Http;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\EnumColumn;
use Filament\Forms\Components\Section;
use Illuminate\Support\Collection;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Swapinvidya\HuggingFaceClient\HuggingFaceClient;
use App\Jobs\ProcessEstrategiaAnalysis;

class EstrategiaResource extends Resource
{
    protected static ?string $model = Estrategia::class;

    protected static ?string $navigationIcon = 'heroicon-s-hand-thumb-up';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Ejecución Campaña';
    protected static ?string $label = 'Estrategias';
    protected static ?string $pluralLabel = 'Estrategias';


    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
                ->schema([
                        Section::make('Enfoque psico - político')
                            ->columns(2)
                            ->schema([
                                Forms\Components\Select::make('candidate_id')
                                    ->relationship('candidate', 'name')
                                    ->preload()
                                    ->searchable()
                                    ->label('Candidato')
                                    ->placeholder('Selecciona Candidato'),
                                Forms\Components\Textarea::make('quiereser')
                                    ->label('Porque quiere ser?'),
                                Forms\Components\Textarea::make('determinoimagen')
                                    ->label('Determinó Usted su Propia Imágen?'),
                                Forms\Components\Textarea::make('identificoproblemas')
                                    ->label('Identificó los problemas claves?'),
                                Forms\Components\Textarea::make('identificoseguidores')
                                    ->label('Identificó a sus seguidores?'),
                                Forms\Components\Textarea::make('identificocapacidad')
                                    ->label('Identificó su capacidad de recursos?'),
                                Forms\Components\Textarea::make('iteresproyecto')
                                    ->label('Que es lo que más le interesa que la gente sepa de usted y de su proyecto?'),
                                Forms\Components\Textarea::make('mejorqueotros')
                                    ->label('Que lo hace mejor a usted que a los otros?'),
                        ]),
                        Section::make('Lógica Procedimental')
                            ->columns(2)
                            ->schema([
                                Forms\Components\Textarea::make('Propuesta')
                                    ->label('Propuesta'),
                                Forms\Components\Textarea::make('sectorpriorizado')
                                    ->label('Sector Priorizado'),
                                Forms\Components\Textarea::make('problematicadeterminada')
                                    ->label('Problematica Determinada'),
                                Forms\Components\Textarea::make('objetivogeneral')
                                    ->label('Objetivo General'),
                                Forms\Components\Textarea::make('objetivosestrategicos')
                                    ->label('Objetivos Estratégicos Sectoriales'),
                        ]),
                        Section::make('Planeación')
                            ->columns(2)
                            ->schema([
                                Forms\Components\Textarea::make('planeacionestrategia')
                                    ->label('Planeación Estratégia'),
                                Forms\Components\Textarea::make('plandesarrollo')
                                    ->label('Plan de desarrollo del proceso'),
                                Forms\Components\Textarea::make('planproceso')
                                    ->label('Plan de acción del proceso'),
                                Forms\Components\Textarea::make('planmejoramiento')
                                    ->label('Plan de mejoramiento del proceso'),
                        ]),
                        Section::make('Programática')
                            ->columns(2)
                            ->schema([
                                Forms\Components\Textarea::make('situacionreal')
                                    ->label('Situación Real'),
                                Forms\Components\Textarea::make('insumos')
                                    ->label('Insumos'),
                                Forms\Components\Textarea::make('procesos')
                                    ->label('Procesos'),
                                Forms\Components\Textarea::make('productos')
                                    ->label('productos'),
                                Forms\Components\Textarea::make('resultados')
                                    ->label('Resultados'),
                                Forms\Components\Textarea::make('impactos')
                                    ->label('Impactos'),
                                Forms\Components\Textarea::make('situacionlograble')
                                    ->label('Situación Lograble'),
                                Forms\Components\Textarea::make('analisis')
                                    ->label('Análisis'),
                        ]),
                        Section::make('Análisis Estratégico')
                            ->schema([
                                Forms\Components\Placeholder::make('status')
                                    ->content(fn (Estrategia $record) => match($record->analisis_status ?? 'pendiente') {
                                        'pendiente' => 'Pendiente de análisis',
                                        'procesando' => 'Analizando...',
                                        'completado' => 'Análisis completo',
                                        'error' => 'Error en análisis',
                                        default => 'Estado desconocido'
                                    })
                                    ->hiddenOn('create'),
                            
                                Forms\Components\Textarea::make('analisis')
                                    ->label('Resumen')
                                    ->disabled()
                                    ->columnSpanFull()
                                    ->hiddenOn('create'),
                                    
                                Forms\Components\KeyValue::make('analisis_detallado')
                                    ->label('Análisis Detallado')
                                    ->disabled()
                                    ->columnSpanFull()
                                    ->hiddenOn('create')
                            ])
                            ->hiddenOn('create')
                    ]);
    }

    public static function table(Table $table): Table
    {   
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('candidate.name')
                    ->label('Candidato')
                    ->sortable(),
                Tables\Columns\TextColumn::make('quiereser')
                    ->label('¿Qué quiere ser?')
                    ->limit(50),
                Tables\Columns\TextColumn::make('analisis')
                    ->label('Análisis')
                    ->limit(50),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('analisis_status')
                    ->label('Estado Análisis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pendiente' => 'gray',
                        'procesando' => 'warning',
                        'completado' => 'success',
                        'error' => 'danger',
                        default => 'gray'
                    }),
            ])
            ->actions([
                Action::make('analizar')
                    ->label('Ejecutar Análisis')
                    ->icon('heroicon-o-sparkles')
                    ->action(function (Estrategia $record) {
                        ProcessEstrategiaAnalysis::dispatch($record);
                        Notification::make()
                            ->title('Análisis en proceso')
                            ->body('El análisis se está realizando en segundo plano')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Estrategia $record) => $record->analisis_status !== 'procesando'),
            ]);
    } 

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEstrategias::route('/'),
            'create' => Pages\CreateEstrategia::route('/create'),
            'edit' => Pages\EditEstrategia::route('/{record}/edit'),
        ];
    }
}