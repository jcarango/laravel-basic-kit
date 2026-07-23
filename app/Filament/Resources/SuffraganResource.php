<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SuffraganResource\Pages;
use App\Filament\Resources\SuffraganResource\RelationManagers;
use App\Models\Suffragan;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Models\Category;
use App\Models\Divipol;
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
use Filament\Tables\Columns\HtmlColumn;

class SuffraganResource extends Resource
{
    protected static ?string $model = Suffragan::class;

    protected static ?string $navigationIcon = 'heroicon-s-identification';
    protected static ?int $navigationSort = 13;
    protected static ?string $navigationGroup = 'Control Electoral';
    protected static ?string $label = 'Sufragantes';

    // Hacer que la barra de búsqueda principal consulte la experiencia, educación, perfil y nombre
    protected static ?array $searchableAttributes = [
        'name',
        'lastname',
        'documentationnumber',
        'resume.profile_summary',
        'experience.company',
        'experience.position',
        'education.institution',
        'education.degree',
    ];

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Wizard::make([
                // WIZARD STEP 1: Datos Básicos
                Forms\Components\Wizard\Step::make('Información Básica')
                    ->icon('heroicon-o-identification')
                    ->schema([
                        Section::make('NOMBRE')
                            ->columns(4)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nombre')->required()->maxLength(255),
                                Forms\Components\TextInput::make('lastname')
                                    ->label('Apellido')->required()->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->label('Email')->email()->required()->maxLength(255),
                                Forms\Components\TextInput::make('phone')
                                    ->label('Celular')->tel()->required()->maxLength(15),
                                Forms\Components\Select::make('documentationtype')
                                    ->label('Tipo Doc')
                                    ->options(['cedula' => 'Cédula', 'nuip' => 'NUIP', 'registrocivil' => 'Registro Civíl', 'otro' => 'Otro']),
                                Forms\Components\TextInput::make('documentationnumber')
                                    ->label('Número')->required()->maxLength(255),
                                Forms\Components\TextInput::make('latitude')->label('Latitud')->maxLength(255),
                                Forms\Components\TextInput::make('longitude')->label('Longitud')->maxLength(255),
                            ]),
                        Section::make('Dirección y Redes')
                            ->columns(4)
                            ->schema([
                                Forms\Components\Select::make('country_id')
                                    ->relationship(name : 'country', titleAttribute:'name')
                                    ->label('País')
                                    ->searchable()->preload()->live()
                                    ->afterStateUpdated(function (Set $set) {
                                        $set('state_id',null);
                                        $set('city_id',null);
                                    })->required(),
                                Forms\Components\Select::make('state_id')
                                    ->options(fn (Get $get): Collection => State::query()->where('country_id', $get('country_id'))->pluck('name','id'))
                                    ->label('Departamento')
                                    ->searchable()->preload()->live()
                                    ->afterStateUpdated(fn (Set $set) =>$set('city_id',null))->required(),
                                Forms\Components\Select::make('city_id')
                                    ->options(fn (Get $get): Collection => City::query()->where('state_id', $get('state_id'))->pluck('name','id'))
                                    ->label('Ciudad')
                                    ->searchable()->preload()->required(),
                                Forms\Components\TextInput::make('address')
                                    ->label('Dirección')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                        if (!$state) return;
                                        $city = \App\Models\City::find($get('city_id'))?->name ?? '';
                                        $stateName = \App\Models\State::find($get('state_id'))?->name ?? '';
                                        $country = \App\Models\Country::find($get('country_id'))?->name ?? '';
                                        $fullAddress = "{$state}, {$city}, {$stateName}, {$country}";
                                        $response = \Illuminate\Support\Facades\Http::withHeaders(['User-Agent' => 'Demosol/1.0'])->get('https://nominatim.openstreetmap.org/search', ['q' => $fullAddress, 'format' => 'json', 'limit' => 1])->json();
                                        if (!empty($response) && isset($response[0]['lat']) && isset($response[0]['lon'])) {
                                            $set('latitude', $response[0]['lat']);
                                            $set('longitude', $response[0]['lon']);
                                        }
                                    })->required(),
                                Forms\Components\TextInput::make('profession')->label('Profesión')->maxLength(255),
                                Forms\Components\TextInput::make('facebook')->label('Facebook')->maxLength(255),
                                Forms\Components\TextInput::make('instagram')->label('Instagram')->maxLength(255),
                                Forms\Components\FileUpload::make('photo')->label('Foto de Perfil')->preserveFilenames()->disk('public')->directory('profile'),
                            ]),
                        Section::make('Electoral y Sistema')
                            ->columns(4)
                            ->schema([
                                Forms\Components\Select::make('categories_id')
                                    ->label('Categoría')
                                    ->relationship('category', 'name')->preload()->searchable(),
                                Forms\Components\Select::make('voter_type')
                                    ->label('Intención')->options(['Duro' => 'Voto Duro', 'Blando' => 'Voto Blando', 'Opinión' => 'Voto de Opinión'])->default('Opinión')->required(),
                                Forms\Components\Select::make('divipol_id')
                                    ->label('Puesto de Votación (Asignación Principal)')
                                    ->options(\App\Models\Divipol::pluck('nom_puesto', 'id'))->searchable()->preload()->reactive()->required()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $divipol = \App\Models\Divipol::find($state);
                                        $set('votodepartamento', $divipol?->departamento);
                                        $set('votomunicipio', $divipol?->municipio);
                                        $set('votopuesto', $divipol?->nom_puesto);
                                    }),
                                Forms\Components\TextInput::make('mesa')->label('Mesa')->maxLength(10),
                                Forms\Components\Toggle::make('is_leader')
                                    ->label('¿Es Líder?')->inline(false),
                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('assign_witness_form')
                                        ->label('Roles Testigo (Popup)')
                                        ->icon('heroicon-o-shield-check')
                                        ->color('warning')
                                        ->disabled(fn ($record) => $record === null)
                                        ->tooltip(fn ($record) => $record === null ? 'Debes guardar el sufragante primero para habilitar esta opción' : 'Asignar como Testigo y Crear Usuario')
                                        ->modalHeading('Asignar Testigo Electoral e Ingreso al Sistema')
                                        ->form([
                                            Forms\Components\Select::make('divipols')
                                                ->label('Seleccione Divipol(es) a registrar')
                                                ->options(\App\Models\Divipol::pluck('nom_puesto', 'id'))
                                                ->multiple()
                                                ->searchable()
                                                ->preload()
                                                ->default(fn (Suffragan $record) => clone $record ? $record->divipols->pluck('id')->toArray() : [])
                                                ->required(),
                                            Forms\Components\Toggle::make('create_user')
                                                ->label('Habilitar Entrada al Sistema (Login de usuario)')
                                                ->helperText('Esto creará un usuario usando el Teléfono como Correo y Clave temporal')
                                                ->default(true)
                                                ->visible(fn (Suffragan $record) => clone $record ? empty($record->user_id) : false),
                                        ])
                                        ->action(function (Suffragan $record, array $data) {
                                            $record->update(['is_witness' => true]);
                                            
                                            $syncData = [];
                                            foreach ($data['divipols'] as $d_id) {
                                                $syncData[$d_id] = ['valor' => 'testigo'];
                                            }
                                            $record->divipols()->sync($syncData);
                                            
                                            if (isset($data['create_user']) && $data['create_user'] && empty($record->user_id)) {
                                                $phoneEmail = $record->phone;
                                                if (!empty($phoneEmail)) {
                                                    if (!str_contains($phoneEmail, '@')) {
                                                        $phoneEmail .= '@demosol.com';
                                                    }
                                                    
                                                    $user = \App\Models\User::firstOrCreate(
                                                        ['email' => $phoneEmail],
                                                        [
                                                            'name' => trim($record->name . ' ' . $record->lastname),
                                                            'password' => \Illuminate\Support\Facades\Hash::make($record->phone),
                                                            'habeas_data_accepted' => true,
                                                            'is_active' => true,
                                                        ]
                                                    );
                                                    
                                                    $record->update(['user_id' => $user->id]);
                                                }
                                            }
                                            
                                            \Filament\Notifications\Notification::make()
                                                ->title('Asignación Completa')
                                                ->body('El Sufragante ahora es Testigo Electoral y sus Divipoles han sido configuradas.')
                                                ->success()
                                                ->send();
                                        })
                                ])
                                ->columnSpan(2),
                                Forms\Components\Select::make('candidate_id')
                                    ->relationship('candidate', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->label('Candidato Principal'),
                                Forms\Components\TextInput::make('votodepartamento')->label('Depto. Votación')->disabled(),
                                Forms\Components\TextInput::make('votomunicipio')->label('Mpio. Votación')->disabled(),
                                Forms\Components\TextInput::make('votopuesto')->label('Puesto Votación')->disabled(),
                                Forms\Components\Placeholder::make('candidate_preview')
                                    ->label('Visualización de Candidato y Partido')
                                    ->content(function (Get $get) {
                                        $candidateId = $get('candidate_id');
                                        if (!$candidateId) {
                                            return 'Ningún candidato seleccionado';
                                        }
                                        $candidate = \App\Models\Candidate::with('partido')->find($candidateId);
                                        if (!$candidate) return 'Candidato no encontrado';

                                        $photoUrl = $candidate->photo ? url('storage/' . $candidate->photo) : null;
                                        $logoUrl = $candidate->partido?->logo ? url('storage/' . $candidate->partido->logo) : null;

                                        $photoImg = $photoUrl ? "<img src='{$photoUrl}' style='width:60px; height:60px; border-radius:50%; object-fit:cover; border:2px solid #6366f1;' />" : "";
                                        $logoImg = $logoUrl ? "<img src='{$logoUrl}' style='width:60px; height:60px; object-fit:contain;' />" : "";

                                        return new \Illuminate\Support\HtmlString("
                                            <div style='display:flex; align-items:center; gap:1rem; padding:12px; background:#f1f5f9; border-radius:8px;'>
                                                {$photoImg}
                                                <div>
                                                    <strong style='font-size:15px; display:block;'>{$candidate->name} {$candidate->lastname}</strong>
                                                    <span style='font-size:12px; color:#475569;'>Cargo: " . ($candidate->cargo_aspira ?? 'Candidato') . "</span><br>
                                                    <span style='font-size:12px; color:#4f46e5; font-weight:bold;'>Partido: " . ($candidate->partido?->name ?? 'N/A') . "</span>
                                                </div>
                                                <div style='margin-left:auto;'>
                                                    {$logoImg}
                                                </div>
                                            </div>
                                        ");
                                    })
                                    ->columnSpanFull(),
                            ]),
                        Section::make('Ubicación Geográfica en Mapa')
                            ->columns(2)
                            ->schema([
                                \Dotswan\MapPicker\Fields\Map::make('location')
                                    ->label('Mapa Interactivo (Mover Marcador)')
                                    ->defaultLocation([4.5709, -74.2973])
                                    ->clickable(true)
                                    ->draggable(true)
                                    ->zoom(12)
                                    ->columnSpanFull(),
                            ]),
                    ]),

                    
                // WIZARD STEP 2: Experiencia
                Forms\Components\Wizard\Step::make('Experiencia Laboral')
                    ->icon('heroicon-o-briefcase')
                    ->schema([
                        Forms\Components\Repeater::make('experience')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('company')->required()->label('Empresa o Campaña'),
                                Forms\Components\TextInput::make('position')->required()->label('Cargo / Rol'),
                                Forms\Components\DatePicker::make('start_date')->required()->label('Fecha Inicio'),
                                Forms\Components\Toggle::make('currently_working')->label('Aún laboro aquí')->live(),
                                Forms\Components\DatePicker::make('end_date')
                                    ->label('Fecha Fin')
                                    ->required(fn (Get $get) => ! $get('currently_working'))
                                    ->hidden(fn (Get $get) => $get('currently_working'))
                                    ->after('start_date'),
                                Forms\Components\Textarea::make('achievements')->columnSpanFull()->label('Logros o Aportes Principales')
                            ])->columns(2)->collapsible()->defaultItems(0),
                    ]),

                // WIZARD STEP 3: Formación Académica
                Forms\Components\Wizard\Step::make('Formación Académica')
                    ->icon('heroicon-o-academic-cap')
                    ->schema([
                        Forms\Components\Repeater::make('education')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('institution')->required()->label('Institución Educativa'),
                                Forms\Components\TextInput::make('degree')->required()->label('Título'),
                                Forms\Components\Select::make('status')->options(['En curso' => 'En curso', 'Graduado' => 'Graduado', 'Abandonado' => 'Abandonado'])->required(),
                                Forms\Components\DatePicker::make('start_date')->label('Inicio'),
                                Forms\Components\DatePicker::make('end_date')->label('Fin')
                            ])->columns(2)->collapsible()->defaultItems(0),
                    ]),

                // WIZARD STEP 4: Perfil Estratégico & Comités
                Forms\Components\Wizard\Step::make('Perfil & Comités')
                    ->icon('heroicon-o-star')
                    ->schema([
                        Forms\Components\Fieldset::make('Información del Perfil HRIS')
                            ->relationship('resume')
                            ->schema([
                                Forms\Components\Textarea::make('profile_summary')
                                    ->label('Resumen de Perfil Profesional')
                                    ->columnSpanFull()
                                    ->rows(3),
                                Forms\Components\Toggle::make('is_available_for_committees')
                                    ->label('Tiene Disponibilidad de Tiempo para Comités y Activismo')
                                    ->columnSpanFull()
                                    ->inline(false)
                            ]),
                            
                        Forms\Components\Repeater::make('suffraganSkills')
                            ->label('Habilidades de Campaña')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('skill_id')->relationship('skill', 'name')->searchable()->preload()->required()->label('Habilidad'),
                                Forms\Components\Select::make('level')->options(['Básico' => 'Básico', 'Intermedio' => 'Intermedio', 'Avanzado' => 'Avanzado', 'Experto' => 'Experto'])->required()->label('Nivel'),
                                Forms\Components\TextInput::make('years_experience')->numeric()->label('Años Exp.'),
                            ])->columns(3)->defaultItems(0),
                        
                        Forms\Components\Repeater::make('committeeSuffragans')
                            ->label('Asignación a Comités')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('committee_id')
                                    ->relationship('committee', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->label('Comité Político'),
                                Forms\Components\Select::make('role')->options(['Miembro' => 'Miembro', 'Coordinador' => 'Coordinador', 'Enlace' => 'Enlace'])->required()->label('Rol Proyectado')
                            ])->columns(2)->defaultItems(0)
                    ]),

                // WIZARD STEP 5: Caracterización Social & Productiva
                Forms\Components\Wizard\Step::make('Caracterización Social')
                    ->icon('heroicon-o-home-modern')
                    ->schema([
                        Section::make('Información General de Caracterización')
                            ->columns(4)
                            ->schema([
                                Forms\Components\TextInput::make('consecutivo')->label('Consecutivo'),
                                Forms\Components\DatePicker::make('characterization_date')->label('Fecha Caracterización'),
                                Forms\Components\TextInput::make('vereda')->label('Vereda'),
                                Forms\Components\TextInput::make('corregimiento')->label('Corregimiento'),
                            ]),

                        Section::make('Información del Predio')
                            ->columns(4)
                            ->schema([
                                Forms\Components\TextInput::make('property_name')->label('Nombre del Predio'),
                                Forms\Components\TextInput::make('total_area')->numeric()->label('Área Total (ha)'),
                                Forms\Components\TextInput::make('available_area')->numeric()->label('Área Disponible (ha)'),
                                Forms\Components\Select::make('cadastral_status')
                                    ->label('Estado Catastral')
                                    ->options([
                                        'Escritura pública' => 'Escritura pública',
                                        'Compraventa' => 'Compraventa',
                                        'Sucesión' => 'Sucesión',
                                        'Otro' => 'Otro',
                                    ]),
                            ]),

                        Section::make('Proyectos y Condición Social')
                            ->columns(3)
                            ->schema([
                                Forms\Components\Select::make('gender')
                                    ->label('Sexo')
                                    ->options(['Masculino' => 'Masculino', 'Femenino' => 'Femenino', 'Otro' => 'Otro']),
                                Forms\Components\Toggle::make('is_project_beneficiary')->label('¿Beneficiario de proyectos?')->live(),
                                Forms\Components\TextInput::make('project_name')->label('Nombre del Proyecto')->hidden(fn (Get $get) => !$get('is_project_beneficiary')),
                                Forms\Components\Toggle::make('has_disability')->label('¿Condición de Discapacidad?')->live(),
                                Forms\Components\TextInput::make('disability_type')->label('Tipo de Discapacidad')->hidden(fn (Get $get) => !$get('has_disability')),
                            ]),

                        Section::make('Línea Productiva')
                            ->columns(3)
                            ->schema([
                                Forms\Components\TextInput::make('livestock_count')->numeric()->label('Número de Animales'),
                                Forms\Components\TextInput::make('species')->label('Especies'),
                                Forms\Components\TextInput::make('unit_of_measure')->label('Unidad de Medida'),
                            ]),

                        Section::make('Grupos Poblacionales')
                            ->schema([
                                Forms\Components\CheckboxList::make('population_groups')
                                    ->label('Seleccionar Grupos Poblacionales')
                                    ->options([
                                        'Mujer Rural' => 'Mujer Rural',
                                        'PDET' => 'PDET',
                                        'PNIS' => 'PNIS',
                                        'Víctima del conflicto' => 'Víctima del conflicto',
                                        'Grupo étnico' => 'Grupo étnico',
                                        'SISBEN' => 'SISBEN',
                                        'Beneficiario de programas' => 'Beneficiario de programas',
                                        'Asociación' => 'Asociación',
                                    ])
                                    ->columns(4),
                            ]),

                        Section::make('Asociaciones & Proyecto Corderos')
                            ->columns(2)
                            ->schema([
                                Forms\Components\Toggle::make('belongs_to_association')->label('¿Pertenece a alguna asociación?')->live(),
                                Forms\Components\TextInput::make('association_name')->label('Nombre de la Asociación')->hidden(fn (Get $get) => !$get('belongs_to_association')),
                                Forms\Components\Toggle::make('knows_lamb_project')->label('¿Conoce el Proyecto de Corderos?')->live(),
                                Forms\Components\TextInput::make('lamb_project_source')->label('¿Cómo se enteró?')->hidden(fn (Get $get) => !$get('knows_lamb_project')),
                            ]),
                    ]),


                // WIZARD STEP 5: Documentos Privacidad
                Forms\Components\Wizard\Step::make('Privacidad')
                    ->icon('heroicon-o-document-check')
                    ->schema([
                        Section::make('Autorización Legal (Habeas Data)')
                            ->description('Ley 1581 de 2012 de Tratamiento de Datos')
                            ->schema([
                                Forms\Components\Toggle::make('habeas_data_accepted')->label('Autorizo el tratamiento de mis datos personales para gestión política y afines')->required()->accepted(),
                            ])
                    ]),
            ])->columnSpanFull()
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->circular()
                    ->label('')
                    ->height(50)
                    ->width(50)
                    ->disk('public'),
                Tables\Columns\TextColumn::make('category.name')
                    ->badge()
                    ->label('')
                    ->color(function (string $state): string {
                        if (str_contains($state, 'Lider')) {
                            return 'success';
                        } elseif (str_contains($state, 'Testigo')) {
                            return 'warning';
                        } elseif (str_contains($state, 'Militante')) {
                            return 'danger';
                        }
                        return 'gray';
                    }),
                Tables\Columns\IconColumn::make('is_leader')
                    ->label('Líder')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_witness')
                    ->label('Testigo')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('candidate.name')
                    ->label('Candidato Principal')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('voter_type')
                    ->label('Tipo Voto')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Duro' => 'success',
                        'Blando' => 'warning',
                        'Opinión' => 'info',
                        default => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('resume.profile_score')
                    ->label('Puntaje Perfil')
                    ->badge()
                    ->color('primary')
                    ->sortable(),
                Tables\Columns\IconColumn::make('resume.is_available_for_committees')
                    ->label('Disponible')
                    ->boolean(),
                Tables\Columns\TextColumn::make('politicalCommittees.name')
                    ->label('Grupos')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('lastname')
                    ->label('Apellido')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Correo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Celular')
                    ->searchable(),
                Tables\Columns\TextColumn::make('documentationtype')
                    ->label('Tipo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('documentationnumber')
                ->label('Número')
                    ->searchable(),
                Tables\Columns\TextColumn::make('country.name')
                    ->numeric()
                    ->label('País')
                    ->sortable(),
                Tables\Columns\TextColumn::make('state.name')
                    ->numeric()
                    ->label('Departamento')
                    ->sortable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->numeric()
                    ->label('Ciudad')
                    ->sortable(),
                Tables\Columns\TextColumn::make('address')
                ->label('Dirección')
                    ->searchable(),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('voter_type')
                    ->label('Intención de Voto')
                    ->options([
                        'Duro' => 'Voto Duro (Seguro)',
                        'Blando' => 'Voto Blando (Dudoso)',
                        'Opinión' => 'Voto de Opinión',
                    ]),
                Tables\Filters\Filter::make('aptos_comites')
                    ->label('Disponibles para Comités')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->whereHas('resume', fn ($q) => $q->where('is_available_for_committees', true))),
                Tables\Filters\SelectFilter::make('politicalCommittees')
                    ->relationship('politicalCommittees', 'name')
                    ->label('Comités Políticos')
                    ->multiple()
                    ->preload(),
                Tables\Filters\SelectFilter::make('skills')
                    ->relationship('skills', 'name')
                    ->label('Habilidades')
                    ->multiple()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('download_pdf')
                        ->label('PDF CV')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('danger')
                        ->action(function (Suffragan $record) {
                            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.suffragan-resume', ['record' => $record]);
                            return response()->streamDownload(fn () => print($pdf->output()), "{$record->name}_{$record->lastname}_CV.pdf");
                        }),
                    Tables\Actions\Action::make('send_whatsapp')
                        ->label('WhatsApp')
                        ->icon('heroicon-o-chat-bubble-oval-left')
                        ->color('success')
                        ->url(function (Suffragan $record) {
                            $phone = preg_replace('/[^0-9]/', '', $record->phone);
                            if (strlen($phone) == 10) {
                                $phone = '57' . $phone;
                            }
                            return $phone ? "https://wa.me/{$phone}?text=Hola!" : null;
                        })
                        ->openUrlInNewTab()
                        ->visible(fn (Suffragan $record) => !empty($record->phone)),
                    Tables\Actions\Action::make('qr_code')
                        ->label('QR Día D')
                        ->icon('heroicon-o-qr-code')
                        ->color('info')
                        ->modalHeading(fn (Suffragan $record) => "Código QR - {$record->name} {$record->lastname}")
                        ->modalContent(fn (Suffragan $record) => view('filament.components.qr-code', ['record' => $record]))
                        ->modalSubmitAction(false) // Remove the submit button
                        ->modalCancelActionLabel('Cerrar'),
                    Tables\Actions\Action::make('assign_witness')
                        ->label('Roles Testigo')
                        ->icon('heroicon-o-shield-check')
                        ->color('warning')
                        ->modalHeading('Asignar Testigo Electoral e Ingreso al Sistema')
                        ->form([
                            Forms\Components\Select::make('divipols')
                                ->label('Seleccione Divipol(es) a registrar')
                                ->options(\App\Models\Divipol::pluck('nom_puesto', 'id'))
                                ->multiple()
                                ->searchable()
                                ->preload()
                                ->default(fn (Suffragan $record) => $record->divipols->pluck('id')->toArray())
                                ->required(),
                            Forms\Components\Toggle::make('create_user')
                                ->label('Habilitar Entrada al Sistema (Login de usuario)')
                                ->helperText('Esto creará un usuario usando el Teléfono como Correo y Clave temporal')
                                ->default(true)
                                ->visible(fn (Suffragan $record) => empty($record->user_id)),
                        ])
                        ->action(function (Suffragan $record, array $data) {
                            $record->update(['is_witness' => true]);
                            
                            $syncData = [];
                            foreach ($data['divipols'] as $d_id) {
                                $syncData[$d_id] = ['valor' => 'testigo'];
                            }
                            $record->divipols()->sync($syncData);
                            
                            if (isset($data['create_user']) && $data['create_user'] && empty($record->user_id)) {
                                $phoneEmail = $record->phone;
                                // Verificamos si podemos crear el usuario
                                if (!empty($phoneEmail)) {
                                    // Laravel usualmente pide formato de email, agregaremos una extension temporal
                                    if (!str_contains($phoneEmail, '@')) {
                                        $phoneEmail .= '@demosol.com';
                                    }
                                    
                                    $user = \App\Models\User::firstOrCreate(
                                        ['email' => $phoneEmail],
                                        [
                                            'name' => trim($record->name . ' ' . $record->lastname),
                                            'password' => \Illuminate\Support\Facades\Hash::make($record->phone),
                                            'habeas_data_accepted' => true,
                                            'is_active' => true,
                                        ]
                                    );
                                    
                                    $record->update(['user_id' => $user->id]);
                                }
                            }
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Asignación Completa')
                                ->body('El Sufragante ahora es Testigo Electoral y sus Divipoles han sido configuradas.')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\DeleteAction::make(),
                ])
                ->label('Acciones')
                ->icon('heroicon-m-bars-3')
                ->tooltip('Mostrar acciones')
            ], position: \Filament\Tables\Enums\ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    \pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction::make()->label('Exportar Excel'),
                    Tables\Actions\BulkAction::make('send_whatsapp')
                        ->label('Enviar WhatsApp')
                        ->icon('heroicon-o-chat-bubble-oval-left-ellipsis')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Enviar Mensaje de WhatsApp')
                        ->modalDescription('¿Seguro que deseas enviar el mensaje a los sufragantes seleccionados? (Se abrirán pestañas nuevas para cada uno)')
                        ->form([
                            Forms\Components\Textarea::make('message')
                                ->label('Mensaje')
                                ->required()
                                ->default('Hola, te recordamos tu compromiso con la campaña.'),
                        ])
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data, \Filament\Forms\ComponentContainer $form) {
                            $message = urlencode($data['message']);
                            
                            $urls = $records->map(function ($record) use ($message) {
                                $phone = preg_replace('/[^0-9]/', '', $record->phone);
                                if (strlen($phone) == 10) {
                                    $phone = '57' . $phone;
                                }
                                return $phone ? "https://wa.me/{$phone}?text={$message}" : null;
                            })->filter()->values();

                            if ($urls->isEmpty()) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Error')
                                    ->body('Ninguno de los sufragantes seleccionados tiene un teléfono válido.')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            // If it's a single record, we can redirect directly
                            if ($urls->count() === 1) {
                                return redirect()->away($urls->first());
                            }

                            // If multiple, we show a notification. Realistically, browsers block multiple window.open()
                            // A better UX in Filament would be a custom modal that lists the links for the user to click.
                            \Filament\Notifications\Notification::make()
                                ->title('Enlaces generados (' . $urls->count() . ')')
                                ->body('Por políticas del navegador, no se pueden abrir múltiples ventanas. Seleccione y envíe uno por uno o use un servicio de envío masivo de mensajería (SMS/API).')
                                ->warning()
                                ->send();
                        })
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\LeaderResourcesRelationManager::class,
            RelationManagers\FamilyMembersRelationManager::class,
            RelationManagers\RequirementsRelationManager::class,
        ];
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuffragans::route('/'),
            'create' => Pages\CreateSuffragan::route('/create'),
            'edit' => Pages\EditSuffragan::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
