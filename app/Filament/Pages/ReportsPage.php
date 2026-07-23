<?php

namespace App\Filament\Pages;

use App\Services\ReportExportService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;

class ReportsPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?int $navigationSort = 17;
    protected static ?string $navigationGroup = 'Control Electoral';
    protected static ?string $title = 'Centro de Reportes y Exportación';

    protected static string $view = 'filament.pages.reports-page';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Filtros Generales de Exportación')
                    ->columns(4)
                    ->schema([
                        Forms\Components\Select::make('city_id')
                            ->label('Municipio')
                            ->options(\App\Models\City::pluck('name', 'id'))
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('barrio')
                            ->label('Barrio'),
                        Forms\Components\TextInput::make('corregimiento')
                            ->label('Corregimiento'),
                        Forms\Components\TextInput::make('vereda')
                            ->label('Vereda'),
                        Forms\Components\Select::make('candidate_id')
                            ->label('Candidato')
                            ->options(\App\Models\Candidate::get()->pluck('full_name', 'id'))
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('partido_id')
                            ->label('Partido')
                            ->options(\App\Models\Partido::pluck('name', 'id'))
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('leader_id')
                            ->label('Líder Responsable')
                            ->options(\App\Models\Suffragan::where('is_leader', true)->get()->pluck('name', 'id'))
                            ->searchable()
                            ->preload(),
                        Forms\Components\DatePicker::make('date_from')
                            ->label('Fecha Desde'),
                        Forms\Components\DatePicker::make('date_to')
                            ->label('Fecha Hasta'),
                        Forms\Components\Toggle::make('only_opponents')
                            ->label('Solo Opositores'),
                    ]),
            ])
            ->statePath('data');
    }

    public function exportPdf()
    {
        $filters = $this->form->getState();
        return ReportExportService::exportSuffragansPdf($filters);
    }

    public function exportExcel()
    {
        $filters = $this->form->getState();
        return ReportExportService::exportSuffragansExcel($filters);
    }
}
