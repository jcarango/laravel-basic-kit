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
                Forms\Components\Section::make('Información de la Reunión')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->label('Nombre de la Reunión')
                            ->maxLength(255),
                        Forms\Components\ColorPicker::make('color')
                            ->required()
                            ->label('Color en el Calendario'),
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->label('Propósito de la Reunión')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Asociación Política')
                    ->schema([
                        Forms\Components\Select::make('candidate_id')
                            ->label('Candidato Representado')
                            ->relationship('candidate', 'name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} {$record->lastname}")
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('suffragan_id')
                            ->label('Líder Responsable')
                            ->relationship(
                                'leader', 
                                'name', 
                                fn (Builder $query) => $query->where('is_leader', true)
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} {$record->lastname}")
                            ->searchable()
                            ->preload(),
                    ])->columns(2),

                Forms\Components\Section::make('Programación')
                    ->schema([
                        Forms\Components\DateTimePicker::make('starts_at')
                            ->label('Fecha y Hora de Inicio')
                            ->required(),
                        Forms\Components\DateTimePicker::make('ends_at')
                            ->label('Fecha y Hora de Finalización')
                            ->required(),
                    ])->columns(2),
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
                    ->label('')
                    ->height(80)
                    ->width(80)
                    ->url(fn ($record) => url('public/' . $record->qr_code_path)) 
                    ->disk('public'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('color')
                    ->label('Color')
                    ->formatStateUsing(function (string $state) {
                        return "<div style='display: flex; align-items: center; gap: 0.5rem;'>
                                    <span style='display: inline-block; width: 16px; height: 16px; border-radius: 4px; background-color: {$state}; border: 1px solid #ccc;'></span>
                                    {$state}
                                </div>";
                    })
                    ->html(),
                Tables\Columns\TextColumn::make('starts_at')
                    ->label('Inicio')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ends_at')
                    ->label('Final')
                    ->dateTime()
                    ->sortable(),
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
                // Nueva acción para generar PDF
                Tables\Actions\Action::make('pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function (Event $event) {
                        // Generar PDF
                        $qrCode = url('/attendance/' . $event->id);
                        $pdf = Pdf::loadView('events.pdf', [
                            'event' => $event,
                            'qrCode' => $event->qr_code_path 
                                ? public_path('storage/' . $event->qr_code_path)
                                : $this->generateQRCode($event)
                        ]);
                        
                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            "evento-{$event->id}.pdf"
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
