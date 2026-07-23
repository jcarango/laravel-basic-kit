<?php

namespace App\Services;

use App\Models\Candidate;
use App\Models\Event;
use App\Models\FamilyMember;
use App\Models\Requirement;
use App\Models\Suffragan;
use App\Models\Survey;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportExportService
{
    public static function buildSuffraganQuery(array $filters)
    {
        $query = Suffragan::query()->with(['candidate', 'city', 'divipol']);

        if (!empty($filters['city_id'])) {
            $query->where('city_id', $filters['city_id']);
        }
        if (!empty($filters['barrio'])) {
            $query->where('address', 'like', "%{$filters['barrio']}%");
        }
        if (!empty($filters['corregimiento'])) {
            $query->where('corregimiento', $filters['corregimiento']);
        }
        if (!empty($filters['vereda'])) {
            $query->where('vereda', $filters['vereda']);
        }
        if (!empty($filters['candidate_id'])) {
            $query->where('candidate_id', $filters['candidate_id']);
        }
        if (!empty($filters['partido_id'])) {
            $query->whereHas('candidate', fn ($q) => $q->where('partido_id', $filters['partido_id']));
        }
        if (!empty($filters['leader_id'])) {
            $query->where('suffragan_id', $filters['leader_id']);
        }
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
        if (!empty($filters['only_opponents']) && $filters['only_opponents']) {
            $query->whereHas('candidate', fn ($q) => $q->where('is_opponent', true));
        }

        return $query;
    }

    public static function exportSuffragansPdf(array $filters)
    {
        $records = static::buildSuffraganQuery($filters)->get();
        $pdf = Pdf::loadView('pdf.suffragans-report', ['records' => $records, 'filters' => $filters]);
        return response()->streamDownload(fn () => print($pdf->output()), "reporte_sufragantes_" . date('Y-m-d') . ".pdf");
    }

    public static function exportSuffragansExcel(array $filters)
    {
        $records = static::buildSuffraganQuery($filters)->get();

        $data = $records->map(function ($s) {
            return [
                'ID' => $s->id,
                'Nombre' => $s->name,
                'Apellido' => $s->lastname,
                'Documento' => $s->documentationnumber,
                'Celular' => $s->phone,
                'Email' => $s->email,
                'Ciudad' => $s->city?->name,
                'Dirección' => $s->address,
                'Candidato' => $s->candidate?->name . ' ' . $s->candidate?->lastname,
                'Tipo Voto' => $s->voter_type,
                'Es Líder' => $s->is_leader ? 'Sí' : 'No',
                'Es Testigo' => $s->is_witness ? 'Sí' : 'No',
            ];
        });

        return Excel::download(new GenericExcelExport($data, [
            'ID', 'Nombre', 'Apellido', 'Documento', 'Celular', 'Email', 'Ciudad', 'Dirección', 'Candidato', 'Tipo Voto', 'Es Líder', 'Es Testigo'
        ]), "reporte_sufragantes_" . date('Y-m-d') . ".xlsx");
    }
}

class GenericExcelExport implements FromCollection, WithHeadings
{
    protected $collection;
    protected $headings;

    public function __construct($collection, array $headings)
    {
        $this->collection = $collection;
        $this->headings = $headings;
    }

    public function collection()
    {
        return $this->collection;
    }

    public function headings(): array
    {
        return $this->headings;
    }
}
