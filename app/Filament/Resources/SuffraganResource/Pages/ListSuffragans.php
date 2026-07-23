<?php

namespace App\Filament\Resources\SuffraganResource\Pages;

use App\Filament\Resources\SuffraganResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ListRecords;


class ListSuffragans extends ListRecords
{
    protected static string $resource = SuffraganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('download_template')
                ->label('Descargar Plantilla Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')
                ->action(function () {
                    return \Maatwebsite\Excel\Facades\Excel::download(
                        new \App\Exports\SuffraganTemplateExport, 
                        'plantilla_importacion_sufragantes.xlsx'
                    );
                }),
            Actions\Action::make('import_excel')
                ->label('Importar Excel Sufragantes')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->modalHeading('Importación Masiva de Sufragantes')
                ->modalDescription('Cargue un archivo Excel (.xlsx) con el listado de sufragantes y seleccione el Líder Responsable que realiza la carga.')
                ->form([
                    Forms\Components\Select::make('leader_id')
                        ->label('Líder Responsable (Carga los sufragantes)')
                        ->options(\App\Models\Suffragan::where('is_leader', true)->get()->mapWithKeys(fn ($s) => [$s->id => "{$s->name} {$s->lastname} - Doc: {$s->documentationnumber}"]))
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\Select::make('candidate_id')
                        ->label('Candidato Asociado')
                        ->options(\App\Models\Candidate::all()->mapWithKeys(fn ($c) => [$c->id => "{$c->name} {$c->lastname}"]))
                        ->searchable()
                        ->preload(),
                    Forms\Components\Select::make('default_city_id')
                        ->label('Municipio por Defecto (si no figura en Excel)')
                        ->options(\App\Models\City::pluck('name', 'id'))
                        ->searchable()
                        ->preload(),
                    Forms\Components\FileUpload::make('attachment')
                        ->label('Archivo Excel (.xlsx, .xls)')
                        ->disk('public')
                        ->directory('imports')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $filePath = storage_path('app/public/' . $data['attachment']);

                    $importer = new \App\Imports\SuffraganImport(
                        $data['leader_id'] ?? null,
                        $data['candidate_id'] ?? null,
                        $data['default_city_id'] ?? null
                    );

                    \Maatwebsite\Excel\Facades\Excel::import($importer, $filePath);

                    \Filament\Notifications\Notification::make()
                        ->title('Importación Completada')
                        ->body("Se cargaron {$importer->importedCount} sufragantes exitosamente asignados al Líder. Se omitieron {$importer->skippedCount} registros duplicados o inválidos.")
                        ->success()
                        ->send();
                }),
        ];
    }

}
