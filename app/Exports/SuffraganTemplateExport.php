<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SuffraganTemplateExport implements FromArray, WithHeadings, WithStyles
{
    public function array(): array
    {
        return [
            [
                'Juan Carlos',
                'Pérez Gómez',
                'cedula',
                '1020304050',
                '3001234567',
                'juan.perez@example.com',
                'Calle 10 # 5-20',
                'Medellín',
                'Duro',
                '5',
                'Ejemplo de observación',
            ],
            [
                'María Elena',
                'Rodríguez',
                'cedula',
                '9876543210',
                '3109876543',
                'maria.rodriguez@example.com',
                'Carrera 15 # 40-12',
                'Bogotá',
                'Opinión',
                '12',
                'Contactada por evento',
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'nombre',
            'apellido',
            'tipo_documento',
            'numero_documento',
            'celular',
            'email',
            'direccion',
            'municipio',
            'intencion_voto',
            'mesa',
            'observaciones',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => '1E3A8A']]],
        ];
    }
}
