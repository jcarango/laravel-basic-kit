<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Sufragantes</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        h2 { text-align: center; color: #1e3a8a; margin-bottom: 5px; }
        p.subtitle { text-align: center; color: #64748b; margin-top: 0; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #cbd5e1; padding: 6px; text-align: left; }
        th { background-color: #f1f5f9; color: #1e293b; font-weight: bold; }
        tr:nth-child(even) { background-color: #f8fafc; }
        .badge { padding: 3px 6px; border-radius: 4px; color: white; font-size: 9px; }
        .bg-success { background-color: #10b981; }
        .bg-danger { background-color: #ef4444; }
    </style>
</head>
<body>
    <h2>REPORTE GENERAL DE SUFRAGANTES</h2>
    <p class="subtitle">Generado el {{ now()->format('d/m/Y H:i') }} | Total registros: {{ count($records) }}</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Documento</th>
                <th>Celular</th>
                <th>Ciudad</th>
                <th>Candidato</th>
                <th>Tipo Voto</th>
                <th>Líder</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $index => $r)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $r->name }} {{ $r->lastname }}</td>
                    <td>{{ $r->documentationnumber }}</td>
                    <td>{{ $r->phone }}</td>
                    <td>{{ $r->city?->name }}</td>
                    <td>{{ $r->candidate?->name }} {{ $r->candidate?->lastname }}</td>
                    <td>{{ $r->voter_type }}</td>
                    <td>{{ $r->is_leader ? 'Sí' : 'No' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
