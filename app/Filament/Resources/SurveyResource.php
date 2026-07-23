<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SurveyResource\Pages;
use App\Filament\Resources\SurveyResource\RelationManagers;
use App\Models\Survey;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SurveyResource extends Resource
{
    protected static ?string $model = Survey::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';
    protected static ?int $navigationSort = 16;
    protected static ?string $navigationGroup = 'Control Electoral';
    protected static ?string $label = 'Encuesta';
    protected static ?string $pluralLabel = 'Sistema de Encuestas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Encuesta')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Título de la Encuesta')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_active')
                            ->label('¿Activa?')
                            ->default(true),
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Fecha de Inicio'),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Fecha de Cierre'),
                        Forms\Components\Textarea::make('description')
                            ->label('Descripción / Propósito')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Segmentación / Asignación')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('city_id')
                            ->label('Municipio Destino')
                            ->relationship('city', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('barrio')
                            ->label('Barrio Destino'),
                        Forms\Components\Select::make('event_id')
                            ->label('Evento Político Relacionado')
                            ->relationship('event', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('candidate_id')
                            ->label('Candidato Asociado')
                            ->relationship('candidate', 'name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} {$record->lastname}")
                            ->searchable()
                            ->preload(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activa')
                    ->boolean(),
                Tables\Columns\TextColumn::make('city.name')
                    ->label('Municipio'),
                Tables\Columns\TextColumn::make('questions_count')
                    ->counts('questions')
                    ->label('Preguntas'),
                Tables\Columns\TextColumn::make('responses_count')
                    ->counts('responses')
                    ->label('Respuestas'),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Inicio')
                    ->date(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Fin')
                    ->date(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Activas'),
            ])
            ->actions([
                Tables\Actions\Action::make('fill_survey')
                    ->label('Responder Encuesta')
                    ->icon('heroicon-o-pencil-square')
                    ->color('success')
                    ->url(fn (Survey $record) => route('survey.form', ['survey' => $record]))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('view_responses')
                    ->label('Ver Resultados')
                    ->icon('heroicon-o-chart-pie')
                    ->color('info')
                    ->modalHeading(fn (Survey $record) => "Resultados de Encuesta: {$record->title}")
                    ->modalContent(function (Survey $record) {
                        $responsesCount = $record->responses()->count();
                        $questions = $record->questions()->with('answers')->get();

                        $questionsHtml = '';
                        foreach ($questions as $q) {
                            $answers = \App\Models\SurveyAnswer::where('survey_question_id', $q->id)->pluck('answer_value');
                            $answersList = $answers->take(5)->map(fn ($a) => "<li style='font-size:12px; color:#475569;'>{$a}</li>")->implode('');
                            
                            $questionsHtml .= "
                                <div style='margin-bottom:15px; padding:10px; background:#f8fafc; border-radius:6px; border:1px solid #e2e8f0;'>
                                    <strong style='font-size:13px; color:#1e293b;'>Pregunta: {$q->question_text}</strong> <span style='font-size:10px; background:#e2e8f0; padding:2px 6px; border-radius:4px;'>{$q->type}</span>
                                    <p style='font-size:11px; color:#64748b; margin:4px 0;'>Total Respuestas: " . $answers->count() . "</p>
                                    <ul style='margin:0; padding-left:15px;'>{$answersList}</ul>
                                </div>
                            ";
                        }

                        return new \Illuminate\Support\HtmlString("
                            <div>
                                <p style='font-size:14px; font-weight:bold; margin-bottom:10px;'>Total Encuestas Respondidas: <span style='color:#2563eb;'>{$responsesCount}</span></p>
                                {$questionsHtml}
                            </div>
                        ");
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar'),
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
            RelationManagers\QuestionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSurveys::route('/'),
            'create' => Pages\CreateSurvey::route('/create'),
            'edit' => Pages\EditSurvey::route('/{record}/edit'),
        ];
    }
}
