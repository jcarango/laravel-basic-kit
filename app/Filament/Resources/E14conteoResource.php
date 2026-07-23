<?php

namespace App\Filament\Resources;

use App\Filament\Resources\E14conteoResource\Pages;
use App\Filament\Resources\E14conteoResource\RelationManagers;
use App\Models\Candidate;
use App\Models\E14conteo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\EnumColumn;
use Filament\Forms\Components\Section;
use Illuminate\Support\Collection;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Swapinvidya\LaravelHuggingFaceClient\Facades\HuggingFace;
use Filament\Forms\Components\BelongsToManyRelationManager;
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\Repeater;
use Illuminate\Support\Facades\Log;


class E14conteoResource extends Resource
{
    protected static ?string $model = E14conteo::class;

    protected static ?string $navigationIcon = 'heroicon-s-clipboard-document-check';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'Ejecución Campaña';
    protected static ?string $label = 'E14 - Conteos';
    protected static ?string $pluralLabel = 'E14 - Conteos';


    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        $calculateSum = function (Get $get, Set $set) {
            $total_sufragantes = (int) $get('total_sufragantes_e11');
            if ($total_sufragantes === 0) {
                return;
            }

            $candidates = $get('candidates') ?? [];
            $candidateVotes = 0;
            foreach ($candidates as $candidate) {
                $candidateVotes += (int) ($candidate['votos'] ?? 0);
            }
            
            $votos_en_blanco = (int) ($get('votos_en_blanco') ?? 0);
            $votos_nulos = (int) ($get('votos_nulos') ?? 0);
            $votos_no_marcados = (int) ($get('votos_no_marcados') ?? 0);
            
            $total_sum = $candidateVotes + $votos_en_blanco + $votos_nulos + $votos_no_marcados;
            
            // Set the dummy field used for placeholder
            $set('suma_total_calculada', $total_sum);

            if ($total_sum !== $total_sufragantes && $total_sum > 0) {
                \Filament\Notifications\Notification::make()
                    ->warning()
                    ->title('Descuadre de Votos')
                    ->body("La suma de votos ($total_sum) no es igual a los Votantes E-11 ($total_sufragantes).")
                    ->send();
            }
        };

        return $form
            ->schema([
                Forms\Components\Card::make([
                    Forms\Components\Repeater::make('candidates')
                        ->schema([
                            Forms\Components\Select::make('candidate_id')
                                ->relationship('candidates', 'name')
                                ->getOptionLabelFromRecordUsing(fn (Candidate $record) => "{$record->name} {$record->lastname}")
                                ->preload()
                                ->label('Candidato')
                                ->required()
                                ->searchable(),
                            
                            Forms\Components\TextInput::make('votos')
                                ->numeric()
                                ->label('Votos E14')
                                ->live(debounce: 500)
                                ->afterStateUpdated($calculateSum)
                                ->required(),
                        ])
                        ->live(debounce: 500)
                        ->afterStateUpdated($calculateSum)
                        ->columns(2),
                    ]),
            Forms\Components\Card::make([
                Forms\Components\Select::make('divipol_id')
                    ->label('Puesto de Votación (Divipol)')
                    ->options(function () {
                        $witness = \App\Models\Suffragan::where('user_id', auth()->id())->where('is_witness', true)->first();
                        if ($witness && $witness->divipols()->count() > 0) {
                            return $witness->divipols()->pluck('nom_puesto', 'divipols.id');
                        }
                        return \App\Models\Divipol::pluck('nom_puesto', 'id');
                    })
                    ->default(function () {
                        $witness = \App\Models\Suffragan::where('user_id', auth()->id())->where('is_witness', true)->first();
                        if ($witness) {
                            return $witness->divipols()->first()?->id;
                        }
                        return null;
                    })
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('mesa')
                    ->default(function () {
                        $witness = \App\Models\Suffragan::where('user_id', auth()->id())->where('is_witness', true)->first();
                        if ($witness) {
                            return $witness->mesa;
                        }
                        return null;
                    })
                    ->required()
                    ->maxLength(255),
                Forms\Components\Hidden::make('user_id')
                    ->default(fn () => auth()->id()),
                Forms\Components\TextInput::make('total_sufragantes_e11')
                    ->label('Total votantes E-11')
                    ->numeric()
                    ->live(debounce: 500)
                    ->afterStateUpdated($calculateSum)
                    ->maxLength(255),
                Forms\Components\TextInput::make('total_votos_urna')
                    ->label('V. Urna')
                    ->numeric()
                    ->maxLength(255),
                Forms\Components\TextInput::make('total_votos_incinerados')
                    ->label('V. Incinerados')
                    ->numeric()
                    ->maxLength(255),
                Forms\Components\TextInput::make('votos_en_blanco')
                    ->label('V. en Blanco')
                    ->numeric()
                    ->live(debounce: 500)
                    ->afterStateUpdated($calculateSum)
                    ->maxLength(255),
                Forms\Components\TextInput::make('votos_nulos')
                    ->label('V. Nulos')
                    ->numeric()
                    ->live(debounce: 500)
                    ->afterStateUpdated($calculateSum)
                    ->maxLength(255),
                Forms\Components\TextInput::make('votos_no_marcados')
                    ->label('V. No Marcados')
                    ->numeric()
                    ->live(debounce: 500)
                    ->afterStateUpdated($calculateSum)
                    ->maxLength(255),
                Forms\Components\Placeholder::make('estado_votos')
                    ->label('Estado / Suma Votos')
                    ->content(function (Get $get) {
                        $candidates = $get('candidates') ?? [];
                        $candidateVotes = 0;
                        foreach ($candidates as $candidate) {
                            $candidateVotes += (int) ($candidate['votos'] ?? 0);
                        }
                        
                        $votos_en_blanco = (int) ($get('votos_en_blanco') ?? 0);
                        $votos_nulos = (int) ($get('votos_nulos') ?? 0);
                        $votos_no_marcados = (int) ($get('votos_no_marcados') ?? 0);
                        
                        $total_sum = $candidateVotes + $votos_en_blanco + $votos_nulos + $votos_no_marcados;
                        $total_sufragantes = (int) $get('total_sufragantes_e11');
                        
                        if (!$total_sufragantes) {
                            return 'Esperando datos...';
                        }
                        if ($total_sum === $total_sufragantes) {
                            return new \Illuminate\Support\HtmlString("<span style='color: green; font-weight: bold;'>✅ Cuadra ($total_sum)</span>");
                        }
                        return new \Illuminate\Support\HtmlString("<span style='color: red; font-weight: bold;'>❌ Descuadre (Suma: $total_sum | Votantes E-11: $total_sufragantes)</span>");
                    }),
                Forms\Components\Toggle::make('hubo_reconteo')
                    ->label('Reconteo')
                    ->required(),
                Forms\Components\FileUpload::make('photo')
                    ->preserveFilenames()
                    ->disk('public')
                    ->directory('votos'),
            ])
            ->columns(4),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('divipol.nom_puesto')
                    ->label('Puesto de Votación')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mesa')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('photo')
                    ->label('')
                    ->circular()
                    ->height(50)
                    ->width(50)
                    ->url(fn ($record) => url('public/' . $record->image))
                    ->disk('public'),
                Tables\Columns\TextColumn::make('total_sufragantes_e11')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_votos_urna')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_votos_incinerados')
                    ->searchable(),
                Tables\Columns\TextColumn::make('votos_nulos')
                    ->searchable(),
                Tables\Columns\TextColumn::make('votos_no_marcados')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_votos_mesa')
                    ->searchable(),
                Tables\Columns\IconColumn::make('hubo_reconteo')
                    ->boolean(),
                Tables\Columns\IconColumn::make('ai_matched')
                    ->label('Match IA')
                    ->boolean()
                    ->default(null),
                // Nueva columna para la suma de votos de candidatos
                Tables\Columns\TextColumn::make('total_votos_candidatos')
                    ->label('Total Votos Candidatos')
                    ->getStateUsing(function ($record) {
                        return $record->candidate->sum('pivot.votos');
                    }),
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
                Tables\Actions\Action::make('view_ia_match')
                    ->label('Ver Match Guardado')
                    ->icon('heroicon-o-eye')
                    ->color('success')
                    ->visible(fn (E14conteo $record) => $record->ai_matched !== null)
                    ->action(function (E14conteo $record) {
                        $aiData = $record->ai_match_results;
                        if(is_array($aiData)) {
                             $isPerfectMatch = true;
                             $html = "<div style='font-size: 14px;'>";
                             $html .= "<details style='margin-bottom: 15px; background: rgba(0,0,0,0.2); padding: 5px; border-radius: 5px;'><summary style='cursor:pointer; font-weight:bold; color:#fbbf24'>🛠️ Ver JSON (Guardado)</summary><pre style='font-size:11px; white-space:pre-wrap; color:#a1a1aa; margin-top:5px;'>" . json_encode($aiData, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) . "</pre></details>";
                             $html .= "<table style='width:100%; text-align:left; border-collapse: collapse; margin-top:10px;'>";
                             $html .= "<tr style='border-bottom: 1px solid #4ade80;'><th style='padding:5px'>Concepto</th><th style='padding:5px; text-align:center;'>Digitado</th><th style='padding:5px; text-align:center;'>Leído (IA)</th><th style='padding:5px; text-align:center;'>Match</th></tr>";

                             foreach($record->candidate as $cand) {
                                 $aiVotos = '?';
                                 if(isset($aiData['candidatos'])) {
                                     foreach($aiData['candidatos'] as $key => $val) {
                                         similar_text(strtoupper($cand->name), strtoupper($key), $percent);
                                         if($percent > 40) { $aiVotos = $val; break; }
                                     }
                                     if ($aiVotos === '?' && count($aiData['candidatos']) == 1 && $record->candidate->count() == 1) {
                                         $aiVotos = array_values($aiData['candidatos'])[0];
                                     }
                                 }
                                 $matched = ($aiVotos !== '?' && (int)$cand->pivot->votos === (int)$aiVotos);
                                 if(!$matched) $isPerfectMatch = false;
                                 $matchIcon = $matched ? '<span style="color:#4ade80">✅ Match</span>' : '<span style="color:red">❌ Falla</span>';
                                 $html .= "<tr style='border-bottom: 1px solid gray;'><td>Candidato: {$cand->name}</td><td style='text-align:center'>{$cand->pivot->votos}</td><td style='text-align:center; font-weight:bold;'>{$aiVotos}</td><td style='text-align:center'>{$matchIcon}</td></tr>";
                             }

                             $aiBlancos = $aiData['blancos'] ?? '0';
                             $matchBl = ((int)$record->votos_en_blanco === (int)$aiBlancos);
                             if(!$matchBl) $isPerfectMatch = false;
                             $matchIconBl = $matchBl ? '<span style="color:#4ade80">✅ Match</span>' : '<span style="color:red">❌ Falla</span>';
                             $html .= "<tr style='border-bottom: 1px solid gray;'><td>Votos Blancos</td><td style='text-align:center'>{$record->votos_en_blanco}</td><td style='text-align:center; font-weight:bold;'>{$aiBlancos}</td><td style='text-align:center'>{$matchIconBl}</td></tr>";
                             
                             $aiNulos = $aiData['nulos'] ?? '0';
                             $matchNu = ((int)$record->votos_nulos === (int)$aiNulos);
                             if(!$matchNu) $isPerfectMatch = false;
                             $matchIconNu = $matchNu ? '<span style="color:#4ade80">✅ Match</span>' : '<span style="color:red">❌ Falla</span>';
                             $html .= "<tr style='border-bottom: 1px solid gray;'><td>Votos Nulos</td><td style='text-align:center'>{$record->votos_nulos}</td><td style='text-align:center; font-weight:bold;'>{$aiNulos}</td><td style='text-align:center'>{$matchIconNu}</td></tr>";

                             $html .= "</table></div>";
                             
                             // Refresh status in case they modified something
                             $record->update(['ai_matched' => $isPerfectMatch]);
                             
                             \Filament\Notifications\Notification::make()
                                 ->title('Reporte Recuperado de la Base de Datos')
                                 ->body(new \Illuminate\Support\HtmlString($html))
                                 ->success()
                                 ->persistent()
                                 ->send();
                        }
                    }),
                Tables\Actions\Action::make('scan_ia')
                    ->label('IA Escanear E14')
                    ->icon('heroicon-o-cpu-chip')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Lectura de IA (Prueba Free)')
                    ->modalDescription('Esta prueba gratuita usará OCR.Space para sacar el texto de la imagen y tu llave de DeepSeek configurada en tu .env para organizar los votos encontradros.')
                    ->action(function (E14conteo $record) {
                        try {
                            if (!$record->photo) {
                                \Filament\Notifications\Notification::make()->title('No hay foto a escanear.')->danger()->send();
                                return;
                            }
                            
                            $path = storage_path('app/public/' . $record->photo);
                            if(!file_exists($path)) {
                                \Filament\Notifications\Notification::make()->title('Archivo de foto no encontrado en storage. Abre el E14 y súbelo nuevamente.')->danger()->send();
                                return;
                            }
                            
                            $base64 = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($path));
                            
                            // 1. OCR.Space
                            $response = \Illuminate\Support\Facades\Http::asForm()->post('https://api.ocr.space/parse/image', [
                                'apikey' => 'helloworld',
                                'base64Image' => $base64,
                                'language' => 'spa',
                                'isTable' => 'true',
                                'OCREngine' => '2',
                            ]);
                            
                            if($response->successful()) {
                                $data = $response->json();
                                if (isset($data['IsErroredOnProcessing']) && $data['IsErroredOnProcessing'] == true) {
                                     throw new \Exception($data['ErrorMessage'][0] ?? 'Límite de la API Gratuita (Pesa > 1MB)');
                                }
                                $text = $data['ParsedResults'][0]['ParsedText'] ?? 'No se detectó texto legibible.';
                                
                                // 2. DeepSeek (JSON Extraction)
                                $deepseekKey = env('DEEPSEEK_API_KEY');
                                if($deepseekKey && $text !== 'No se detectó texto legibible.') {
                                    $aiResp = \Illuminate\Support\Facades\Http::withToken($deepseekKey)
                                       ->timeout(20)
                                       ->post(env('DEEPSEEK_API_URL', 'https://api.deepseek.com/v1/chat/completions'), [
                                           'model' => env('DEEPSEEK_MODEL', 'deepseek-chat'),
                                           'messages' => [
                                               ['role' => 'system', 'content' => "Eres un robot que lee reportes E-14 desde texto OCR. DEBES extraer los votos y retornar ÚNICAMENTE JSON válido, nada más. Estructura exacta:\n{\n\"candidatos\": {\"Nombre Apellido\": 10},\n\"blancos\": 0,\n\"nulos\": 0\n}"],
                                               ['role' => 'user', 'content' => $text]
                                           ]
                                       ]);
                                       
                                    if($aiResp->successful()) {
                                       $rawContent = $aiResp->json('choices.0.message.content');
                                       
                                       // Extraer todo bloque JSON usando Regex por si mete basura o Markdown
                                       preg_match('/\{.*\}/s', $rawContent, $matches);
                                       $cleanJsonStr = $matches[0] ?? preg_replace('/```(\w+)?|```/', '', trim($rawContent));
                                       $aiData = json_decode($cleanJsonStr, true);
                                       
                                       if(is_array($aiData)) {
                                            $isPerfectMatch = true;
                                            $html = "<div style='font-size: 14px;'>";
                                            $html .= "<details style='margin-bottom: 15px; background: rgba(0,0,0,0.2); padding: 5px; border-radius: 5px;'><summary style='cursor:pointer; font-weight:bold; color:#fbbf24'>🛠️ Ver Estructura JSON (Diagnóstico)</summary><pre style='font-size:11px; white-space:pre-wrap; color:#a1a1aa; margin-top:5px;'>" . json_encode($aiData, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) . "</pre></details>";
                                            $html .= "<table style='width:100%; text-align:left; border-collapse: collapse; margin-top:10px;'>";
                                            $html .= "<tr style='border-bottom: 1px solid #4ade80;'><th style='padding:5px'>Concepto</th><th style='padding:5px; text-align:center;'>Digitado</th><th style='padding:5px; text-align:center;'>Leído (IA)</th><th style='padding:5px; text-align:center;'>Match</th></tr>";

                                            // Candidatos
                                            foreach($record->candidate as $cand) {
                                                $aiVotos = '?';
                                                
                                                if(isset($aiData['candidatos'])) {
                                                    foreach($aiData['candidatos'] as $key => $val) {
                                                        similar_text(strtoupper($cand->name), strtoupper($key), $percent);
                                                        if($percent > 40) {
                                                            $aiVotos = $val; break;
                                                        }
                                                    }
                                                    // Fallback, if there's only 1 candidate in both
                                                    if ($aiVotos === '?' && count($aiData['candidatos']) == 1 && $record->candidate->count() == 1) {
                                                        $aiVotos = array_values($aiData['candidatos'])[0];
                                                    }
                                                }
                                                
                                                $matched = ($aiVotos !== '?' && (int)$cand->pivot->votos === (int)$aiVotos);
                                                if(!$matched) $isPerfectMatch = false;
                                                $matchIcon = $matched ? '<span style="color:#4ade80">✅ Match</span>' : '<span style="color:red">❌ Falla</span>';
                                                $html .= "<tr style='border-bottom: 1px solid gray;'><td>Candidato: {$cand->name}</td><td style='text-align:center'>{$cand->pivot->votos}</td><td style='text-align:center; font-weight:bold;'>{$aiVotos}</td><td style='text-align:center'>{$matchIcon}</td></tr>";
                                            }

                                            // Blancos
                                            $aiBlancos = $aiData['blancos'] ?? '0';
                                            $matchBl = ((int)$record->votos_en_blanco === (int)$aiBlancos);
                                            if(!$matchBl) $isPerfectMatch = false;
                                            $matchIconBl = $matchBl ? '<span style="color:#4ade80">✅ Match</span>' : '<span style="color:red">❌ Falla</span>';
                                            $html .= "<tr style='border-bottom: 1px solid gray;'><td>Votos Blancos</td><td style='text-align:center'>{$record->votos_en_blanco}</td><td style='text-align:center; font-weight:bold;'>{$aiBlancos}</td><td style='text-align:center'>{$matchIconBl}</td></tr>";
                                            
                                            // Nulos
                                            $aiNulos = $aiData['nulos'] ?? '0';
                                            $matchNu = ((int)$record->votos_nulos === (int)$aiNulos);
                                            if(!$matchNu) $isPerfectMatch = false;
                                            $matchIconNu = $matchNu ? '<span style="color:#4ade80">✅ Match</span>' : '<span style="color:red">❌ Falla</span>';
                                            $html .= "<tr style='border-bottom: 1px solid gray;'><td>Votos Nulos</td><td style='text-align:center'>{$record->votos_nulos}</td><td style='text-align:center; font-weight:bold;'>{$aiNulos}</td><td style='text-align:center'>{$matchIconNu}</td></tr>";

                                            $html .= "</table></div>";
                                            
                                            // Save to DB Audit Log!
                                            $record->update([
                                                'ai_match_results' => $aiData,
                                                'ai_matched' => $isPerfectMatch
                                            ]);
                                            
                                            $text = $html . "<br><details><summary style='cursor:pointer; color:gray;'>📄 Ver detalle crudo OCR</summary><p style='font-size:11px; margin-top:5px; color:#a1a1aa;'>" . e($text) . "</p></details>";
                                        } else {
                                           $text = "Fallo interpretando JSON IA:<br>" . e($rawContent) . "<br><br>Texto Original:<br>" . e($text);
                                        }
                                    } else {
                                        $errorJson = json_decode($aiResp->body(), true);
                                        $errorMsg = $errorJson['error']['message'] ?? $aiResp->body();
                                        $text = "<div style='color: #ef4444; border: 1px solid #ef4444; padding: 10px; border-radius: 5px; margin-bottom: 10px;'>
                                            <strong>⚠ Error de API (Inteligencia Artificial):</strong><br>
                                            El servicio de DeepSeek rechazó la lectura (Status " . $aiResp->status() . "). Detalles: " . e($errorMsg) . "
                                        </div>
                                        <details open><summary style='cursor:pointer; font-weight:bold;'>Ver Texto Crudo Extraído (OCR Básico sin organizar)</summary>
                                        <pre style='font-size:11px; margin-top:5px; white-space:pre-wrap;'>" . e($text) . "</pre></details>";
                                    }
                                }
                                
                                \Filament\Notifications\Notification::make()
                                    ->title('Análisis Automatizado Finalizado')
                                    ->body(new \Illuminate\Support\HtmlString($text))
                                    ->success()
                                    ->persistent()
                                    ->send();
                            } else {
                                \Filament\Notifications\Notification::make()->title('Error de conectividad en OCR')->danger()->send();
                            }
                        } catch (\Exception $e) {
                             \Filament\Notifications\Notification::make()
                                 ->title('Error en API')
                                 ->body('OCR Limit: '. $e->getMessage())
                                 ->danger()
                                 ->send();
                        }
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
            'index' => Pages\ListE14conteos::route('/'),
            'create' => Pages\CreateE14conteo::route('/create'),
            'edit' => Pages\EditE14conteo::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        
        // If the user has a Witness profile, only show their own E14 entries
        $witness = \App\Models\Suffragan::where('user_id', auth()->id())->where('is_witness', true)->first();
        if ($witness) {
            $query->where('user_id', auth()->id());
        }
        
        return $query;
    }

    protected function afterSave(array $data, $model): void
    {
        // Verifica si el campo 'candidates' está presente y es un array
        if (isset($data['candidates']) && is_array($data['candidates'])) {
            $syncData = [];
            
            // Recorre cada candidato en el array
            foreach ($data['candidates'] as $candidate) {
                // Verifica que 'candidate_id' y 'votos' estén presentes
                if (isset($candidate['candidate_id'], $candidate['votos'])) {
                    $syncData[$candidate['candidate_id']] = ['votos' => $candidate['votos']];
                }
            }
            
            // Sincroniza los datos en la tabla intermedia
            $model->candidates()->sync($syncData);
        }
    }


}