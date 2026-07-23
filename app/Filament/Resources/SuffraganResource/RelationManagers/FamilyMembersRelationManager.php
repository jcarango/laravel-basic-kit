<?php

namespace App\Filament\Resources\SuffraganResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class FamilyMembersRelationManager extends RelationManager
{
    protected static string $relationship = 'familyMembers';

    protected static ?string $title = 'Núcleo Familiar';
    protected static ?string $modelLabel = 'Familiar';
    protected static ?string $pluralModelLabel = 'Familiares';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre Completo')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('document_number')
                    ->label('Documento')
                    ->maxLength(255),
                Forms\Components\Select::make('relationship')
                    ->label('Parentesco')
                    ->options([
                        'Esposo(a)' => 'Esposo(a)',
                        'Hijo(a)' => 'Hijo(a)',
                        'Padre/Madre' => 'Padre/Madre',
                        'Hermano(a)' => 'Hermano(a)',
                        'Otro' => 'Otro',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('age')
                    ->label('Edad')
                    ->numeric(),
                Forms\Components\Select::make('gender')
                    ->label('Sexo')
                    ->options([
                        'Masculino' => 'Masculino',
                        'Femenino' => 'Femenino',
                        'Otro' => 'Otro',
                    ]),
                Forms\Components\TextInput::make('phone')
                    ->label('Celular')
                    ->tel()
                    ->maxLength(15),
                Forms\Components\TextInput::make('occupation')
                    ->label('Ocupación')
                    ->maxLength(255),
                Forms\Components\TextInput::make('education_level')
                    ->label('Escolaridad')
                    ->maxLength(255),
                Forms\Components\Textarea::make('notes')
                    ->label('Observaciones')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('relationship')
                    ->label('Parentesco')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('document_number')
                    ->label('Documento'),
                Tables\Columns\TextColumn::make('age')
                    ->label('Edad'),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Celular'),
                Tables\Columns\TextColumn::make('occupation')
                    ->label('Ocupación'),
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
