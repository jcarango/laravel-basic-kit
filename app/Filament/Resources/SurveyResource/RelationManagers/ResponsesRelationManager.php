<?php

namespace App\Filament\Resources\SurveyResource\RelationManagers;

use App\Models\Suffragan;
use App\Models\SurveyResponse;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ResponsesRelationManager extends RelationManager
{
    protected static string $relationship = 'responses';

    protected static ?string $title = 'Encuestados & Respuestas';
    protected static ?string $modelLabel = 'Respuesta de Encuesta';
    protected static ?string $pluralModelLabel = 'Respuestas de Encuesta';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('respondent_name')
                    ->label('Nombre del Encuestado')
                    ->required(),
                Forms\Components\TextInput::make('document_number')
                    ->label('Documento / Cédula'),
                Forms\Components\TextInput::make('phone')
                    ->label('Teléfono / Celular'),
                Forms\Components\TextInput::make('email')
                    ->label('Correo Electrónico'),
                Forms\Components\TextInput::make('address')
                    ->label('Dirección'),
                Forms\Components\TextInput::make('latitude')
                    ->label('Latitud GPS'),
                Forms\Components\TextInput::make('longitude')
                    ->label('Longitud GPS'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('respondent_name')
                    ->label('Encuestado')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('document_number')
                    ->label('Documento')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Celular')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->label('Municipio'),
                Tables\Columns\IconColumn::make('has_gps')
                    ->label('Ubicación GPS')
                    ->state(fn (SurveyResponse $record) => !empty($record->latitude) && !empty($record->longitude))
                    ->boolean()
                    ->trueIcon('heroicon-o-map-pin')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('success')
                    ->falseColor('gray'),
                Tables\Columns\IconColumn::make('converted_to_suffragan')
                    ->label('¿Es Sufragante?')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-user-plus')
                    ->trueColor('success')
                    ->falseColor('warning'),
                Tables\Columns\TextColumn::make('submitted_at')
                    ->label('Fecha / Hora')
                    ->dateTime('d/m/Y g:i A')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('convert_to_suffragan')
                    ->label('Convertir en Sufragante')
                    ->icon('heroicon-o-user-plus')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Convertir Encuestado en Sufragante')
                    ->modalDescription('Esto creará un nuevo registro de Sufragante con los datos personales y coordenadas GPS capturados en la encuesta.')
                    ->visible(fn (SurveyResponse $record) => !$record->converted_to_suffragan)
                    ->action(function (SurveyResponse $record) {
                        $docNumber = trim($record->document_number ?? '');

                        $suffragan = null;
                        if (!empty($docNumber)) {
                            $suffragan = Suffragan::where('documentationnumber', $docNumber)->first();
                        }

                        if (!$suffragan) {
                            $suffragan = Suffragan::create([
                                'name' => $record->respondent_name ?? 'Encuestado Sin Nombre',
                                'lastname' => '',
                                'documentationtype' => 'cedula',
                                'documentationnumber' => !empty($docNumber) ? $docNumber : 'ENC-' . time() . rand(10, 99),
                                'phone' => $record->phone,
                                'email' => $record->email,
                                'address' => $record->address,
                                'city_id' => $record->city_id ?? $record->survey?->city_id,
                                'latitude' => $record->latitude,
                                'longitude' => $record->longitude,
                                'candidate_id' => $record->survey?->candidate_id,
                                'voter_type' => 'Opinión',
                                'habeas_data_accepted' => true,
                            ]);
                        } else {
                            $suffragan->update([
                                'phone' => $suffragan->phone ?: $record->phone,
                                'email' => $suffragan->email ?: $record->email,
                                'address' => $suffragan->address ?: $record->address,
                                'latitude' => $suffragan->latitude ?: $record->latitude,
                                'longitude' => $suffragan->longitude ?: $record->longitude,
                            ]);
                        }

                        $record->update([
                            'converted_to_suffragan' => true,
                            'suffragan_id' => $suffragan->id,
                        ]);

                        Notification::make()
                            ->title('¡Conversión Exitosa!')
                            ->body("El encuestado '{$record->respondent_name}' ha sido registrado como Sufragante.")
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('view_gps')
                    ->label('Ver GPS')
                    ->icon('heroicon-o-map-pin')
                    ->color('info')
                    ->url(fn (SurveyResponse $record) => $record->latitude && $record->longitude ? "https://www.google.com/maps?q={$record->latitude},{$record->longitude}" : null)
                    ->openUrlInNewTab()
                    ->visible(fn (SurveyResponse $record) => !empty($record->latitude) && !empty($record->longitude)),

                Tables\Actions\Action::make('view_answers')
                    ->label('Respuestas')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->modalHeading(fn (SurveyResponse $record) => "Respuestas de: {$record->respondent_name}")
                    ->modalContent(function (SurveyResponse $record) {
                        $answers = $record->answers()->with('question')->get();

                        $html = "<div style='font-size:13px;'>";
                        foreach ($answers as $ans) {
                            $questionText = e($ans->question?->question_text ?? 'Pregunta');
                            $val = e($ans->answer_value ?? 'Sin respuesta');
                            $html .= "<div style='margin-bottom:12px; padding:10px; background:#f8fafc; border-radius:6px; border:1px solid #e2e8f0;'>
                                <strong style='color:#1e293b;'>{$questionText}</strong>
                                <p style='color:#0284c7; margin-top:4px; font-weight:bold;'>{$val}</p>
                            </div>";
                        }
                        $html .= "</div>";

                        return new \Illuminate\Support\HtmlString($html);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar'),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
