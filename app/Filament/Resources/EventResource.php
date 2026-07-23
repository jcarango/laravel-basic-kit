<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Filament\Resources\EventResource\RelationManagers;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;


class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-s-presentation-chart-bar';
    protected static ?int $navigationSort = 14;
    protected static ?string $navigationGroup = 'Control Electoral';
    protected static ?string $label = 'Eventos';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('EventLifecycle')
                    ->tabs([
                        // TAB 1: General
                        Forms\Components\Tabs\Tab::make('General')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->label('Nombre del Evento')
                                    ->maxLength(255),
                                Forms\Components\ColorPicker::make('color')
                                    ->required()
                                    ->label('Color en Calendario')
                                    ->default('#3B82F6'),
                                Forms\Components\DatePicker::make('event_date')
                                    ->label('Fecha del Evento'),
                                Forms\Components\DateTimePicker::make('starts_at')
                                    ->label('Hora de Inicio')
                                    ->required(),
                                Forms\Components\DateTimePicker::make('ends_at')
                                    ->label('Hora de Finalización')
                                    ->required(),
                                Forms\Components\TextInput::make('responsible_name')
                                    ->label('Responsable'),
                                Forms\Components\Select::make('city_id')
                                    ->label('Municipio')
                                    ->relationship('city', 'name')
                                    ->searchable()
                                    ->preload(),
                                Forms\Components\TextInput::make('barrio')
                                    ->label('Barrio / Lugar'),
                                Forms\Components\TextInput::make('latitude')
                                    ->label('Latitud'),
                                Forms\Components\TextInput::make('longitude')
                                    ->label('Longitud'),
                                Forms\Components\Select::make('candidate_id')
                                    ->label('Candidato Representado')
                                    ->relationship('candidate', 'name')
                                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} {$record->lastname}")
                                    ->searchable()
                                    ->preload(),
                                Forms\Components\Select::make('suffragan_id')
                                    ->label('Líder Asignado')
                                    ->relationship('leader', 'name', fn (Builder $query) => $query->where('is_leader', true))
                                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} {$record->lastname}")
                                    ->searchable()
                                    ->preload(),
                                Forms\Components\Textarea::make('description')
                                    ->label('Descripción / Propósito')
                                    ->columnSpanFull(),
                            ])->columns(2),

                        // TAB 2: Planeación
                        Forms\Components\Tabs\Tab::make('Planeación')
                            ->icon('heroicon-o-clipboard-document-check')
                            ->schema([
                                Forms\Components\Textarea::make('objectives')
                                    ->label('Objetivos'),
                                Forms\Components\TextInput::make('budget')
                                    ->label('Presupuesto ($)')
                                    ->numeric()
                                    ->prefix('$'),
                                Forms\Components\Textarea::make('resources_needed')
                                    ->label('Recursos Requeridos'),
                                Forms\Components\Textarea::make('staff_needed')
                                    ->label('Personal Necesario'),
                                Forms\Components\Textarea::make('transport_details')
                                    ->label('Detalles de Transporte'),
                                Forms\Components\Textarea::make('catering_details')
                                    ->label('Alimentación / Logística'),
                                Forms\Components\Textarea::make('logistics_notes')
                                    ->label('Notas Generales de Logística')
                                    ->columnSpanFull(),
                            ])->columns(2),

                        // TAB 3: Avanzada
                        Forms\Components\Tabs\Tab::make('Avanzada')
                            ->icon('heroicon-o-wrench-screwdriver')
                            ->schema([
                                Forms\Components\Textarea::make('pre_visits_notes')
                                    ->label('Visitas Previas'),
                                Forms\Components\Textarea::make('pre_meetings_notes')
                                    ->label('Reuniones Previas'),
                                Forms\Components\TextInput::make('permits_status')
                                    ->label('Estado de Permisos (Tramitados/Pendientes)'),
                                Forms\Components\Textarea::make('publicity_notes')
                                    ->label('Publicidad y Difusión'),
                                Forms\Components\Textarea::make('sound_system_notes')
                                    ->label('Sonido / Audiovisuales'),
                                Forms\Components\Textarea::make('stage_notes')
                                    ->label('Tarima / Montaje'),
                                Forms\Components\Textarea::make('security_notes')
                                    ->label('Seguridad y Protocolo'),
                                Forms\Components\Textarea::make('guests_list')
                                    ->label('Lista de Invitados Especiales'),
                            ])->columns(2),

                        // TAB 4: Durante el Evento
                        Forms\Components\Tabs\Tab::make('Durante el Evento')
                            ->icon('heroicon-o-users')
                            ->schema([
                                Forms\Components\TextInput::make('expected_attendance')
                                    ->label('Asistencia Esperada')
                                    ->numeric(),
                                Forms\Components\TextInput::make('real_attendance')
                                    ->label('Asistencia Real')
                                    ->numeric(),
                                Forms\Components\FileUpload::make('photos')
                                    ->label('Fotografías del Evento')
                                    ->multiple()
                                    ->image()
                                    ->disk('public')
                                    ->directory('events/photos')
                                    ->columnSpanFull(),
                                Forms\Components\FileUpload::make('videos')
                                    ->label('Evidencias en Video / Documentos')
                                    ->multiple()
                                    ->disk('public')
                                    ->directory('events/videos')
                                    ->columnSpanFull(),
                                Forms\Components\Textarea::make('during_notes')
                                    ->label('Observaciones Durante la Jornada')
                                    ->columnSpanFull(),
                            ])->columns(2),

                        // TAB 5: Después del Evento
                        Forms\Components\Tabs\Tab::make('Después del Evento')
                            ->icon('heroicon-o-chart-bar')
                            ->schema([
                                Forms\Components\Textarea::make('result_summary')
                                    ->label('Resultado General'),
                                Forms\Components\Textarea::make('political_impact')
                                    ->label('Impacto Político Evaluado'),
                                Forms\Components\Textarea::make('commitments_acquired')
                                    ->label('Compromisos Adquiridos')
                                    ->columnSpanFull(),
                                Forms\Components\Textarea::make('followup_notes')
                                    ->label('Plan de Seguimiento')
                                    ->columnSpanFull(),
                                Forms\Components\FileUpload::make('evidences')
                                    ->label('Evidencias Finales / Firmas / Planillas')
                                    ->multiple()
                                    ->disk('public')
                                    ->directory('events/evidences')
                                    ->columnSpanFull(),
                            ])->columns(2),
                    ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Enlace')
                    ->url(fn ($record) => url('/attendance/' . $record->id . '?leader=' . auth()->id()))
                    ->formatStateUsing(fn () => 'VER ENLACE')
                    ->openUrlInNewTab(),

                Tables\Columns\ImageColumn::make('qr_code_path')
                    ->label('QR')
                    ->height(60)
                    ->width(60)
                    ->disk('public'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('event_date')
                    ->label('Fecha')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('city.name')
                    ->label('Municipio')
                    ->sortable(),

                Tables\Columns\TextColumn::make('expected_attendance')
                    ->label('Esperados')
                    ->numeric(),

                Tables\Columns\TextColumn::make('real_attendance')
                    ->label('Reales')
                    ->numeric(),

                Tables\Columns\TextColumn::make('starts_at')
                    ->label('Inicio')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('danger')
                    ->action(function (Event $record) {
                        $pdf = \App\Services\EventPdfService::generatePdf($record);
                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            "evento-{$record->id}.pdf"
                        );
                    }),
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
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }

    // Función para generar QR si no existe
    protected function generateQRCode(Event $event): string
    {
        $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
            ->size(300)
            ->generate(url('/attendance/' . $event->id));
        
        $path = "qrcodes/event-{$event->id}.png";
        Storage::disk('public')->put($path, $qrCode);
        
        // Actualizar el evento con el nuevo QR
        $event->update(['qr_code_path' => $path]);
        
        return public_path('storage/' . $path);
    }
}
