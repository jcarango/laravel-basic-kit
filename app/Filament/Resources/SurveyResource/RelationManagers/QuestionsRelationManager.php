<?php

namespace App\Filament\Resources\SurveyResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class QuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';

    protected static ?string $title = 'Preguntas de la Encuesta';
    protected static ?string $modelLabel = 'Pregunta';
    protected static ?string $pluralModelLabel = 'Preguntas';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('question_text')
                    ->label('Texto de la Pregunta')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\Select::make('type')
                    ->label('Tipo de Pregunta')
                    ->options([
                        'text' => 'Texto Libre',
                        'number' => 'Número',
                        'date' => 'Fecha',
                        'single_choice' => 'Selección Única',
                        'multiple_choice' => 'Selección Múltiple',
                        'scale' => 'Escala (1 al 5)',
                        'boolean' => 'Sí / No',
                    ])
                    ->required()
                    ->live(),
                Forms\Components\TagsInput::make('options')
                    ->label('Opciones (Escribir cada opción y presionar Enter)')
                    ->placeholder('Opción 1, Opción 2...')
                    ->visible(fn (Forms\Get $get) => in_array($get('type'), ['single_choice', 'multiple_choice'])),
                Forms\Components\TextInput::make('sort_order')
                    ->label('Orden')
                    ->numeric()
                    ->default(1),
                Forms\Components\Toggle::make('is_required')
                    ->label('¿Obligatoria?')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Orden')
                    ->sortable(),
                Tables\Columns\TextColumn::make('question_text')
                    ->label('Pregunta')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color('info'),
                Tables\Columns\IconColumn::make('is_required')
                    ->label('Obligatoria')
                    ->boolean(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
