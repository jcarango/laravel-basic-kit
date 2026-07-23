<?php

namespace App\Imports;

use App\Models\City;
use App\Models\Suffragan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class SuffraganImport implements ToModel, WithHeadingRow, WithChunkReading
{
    protected ?int $leaderId;
    protected ?int $candidateId;
    protected ?int $defaultCityId;

    public int $importedCount = 0;
    public int $skippedCount = 0;

    public function __construct(?int $leaderId = null, ?int $candidateId = null, ?int $defaultCityId = null)
    {
        $this->leaderId = $leaderId;
        $this->candidateId = $candidateId;
        $this->defaultCityId = $defaultCityId;
    }

    public function model(array $row)
    {
        $docNumber = trim($row['numero_documento'] ?? '');
        if (empty($docNumber) || empty($row['nombre'])) {
            $this->skippedCount++;
            return null;
        }

        // Evitar duplicados por número de documento
        $existing = Suffragan::where('documentationnumber', $docNumber)->first();
        if ($existing) {
            $this->skippedCount++;
            return null;
        }

        // Resolver Municipio por Nombre o usar por defecto
        $cityId = $this->defaultCityId;
        if (!empty($row['municipio'])) {
            $matchedCity = City::where('name', 'like', trim($row['municipio']))->first();
            if ($matchedCity) {
                $cityId = $matchedCity->id;
            }
        }

        // Tipo de documento normalizado
        $docType = strtolower(trim($row['tipo_documento'] ?? 'cedula'));
        if (!in_array($docType, ['cedula', 'nuip', 'registrocivil', 'otro'])) {
            $docType = 'cedula';
        }

        // Intención de voto
        $voterType = trim($row['intencion_voto'] ?? 'Opinión');
        if (!in_array($voterType, ['Duro', 'Blando', 'Opinión'])) {
            $voterType = 'Opinión';
        }

        $this->importedCount++;

        return new Suffragan([
            'name' => trim($row['nombre']),
            'lastname' => trim($row['apellido'] ?? ''),
            'documentationtype' => $docType,
            'documentationnumber' => $docNumber,
            'phone' => trim($row['celular'] ?? ''),
            'email' => trim($row['email'] ?? ''),
            'address' => trim($row['direccion'] ?? ''),
            'city_id' => $cityId,
            'country_id' => 1, // Colombia por defecto
            'suffragan_id' => $this->leaderId, // Líder asignado
            'candidate_id' => $this->candidateId, // Candidato asignado
            'voter_type' => $voterType,
            'mesa' => trim($row['mesa'] ?? ''),
            'habeas_data_accepted' => true,
        ]);
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
