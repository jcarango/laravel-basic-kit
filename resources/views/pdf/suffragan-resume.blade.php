<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Hoja de Vida Electoral - {{ $record->name }} {{ $record->lastname }}</title>
    <style>
        @page {
            margin: 0px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            background-color: #f7f9fa;
        }
        .sidebar {
            width: 30%;
            float: left;
            background-color: #1a202c;
            color: #f7fafc;
            height: 100%;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            padding: 40px 20px;
            box-sizing: border-box;
        }
        .main-content {
            width: 70%;
            float: right;
            padding: 40px 40px 40px 30px;
            box-sizing: border-box;
            background-color: #ffffff;
            min-height: 100vh;
        }
        .profile-image-container {
            text-align: center;
            margin-bottom: 25px;
        }
        .profile-image {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            border: 4px solid #4a5568;
            object-fit: cover;
        }
        .sidebar h2 {
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 1px solid #4a5568;
            padding-bottom: 5px;
            margin-top: 30px;
            margin-bottom: 15px;
            color: #a0aec0;
        }
        .sidebar p, .sidebar li {
            font-size: 13px;
            color: #e2e8f0;
            margin-bottom: 5px;
        }
        .sidebar ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }
        .main-title {
            color: #2b6cb0;
            margin-top: 0;
            margin-bottom: 5px;
            font-size: 32px;
            font-weight: 700;
        }
        .sub-title {
            color: #718096;
            font-size: 18px;
            font-weight: 300;
            margin-top: 0;
            margin-bottom: 30px;
        }
        .content-section {
            margin-bottom: 30px;
        }
        .section-header {
            color: #2b6cb0;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 2px solid #ebf8ff;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        .item-row {
            margin-bottom: 15px;
        }
        .item-title {
            font-weight: bold;
            color: #2d3748;
            font-size: 15px;
        }
        .item-subtitle {
            color: #4a5568;
            font-size: 14px;
            font-weight: 600;
        }
        .item-date {
            color: #718096;
            font-size: 13px;
            font-style: italic;
        }
        .item-desc {
            font-size: 13px;
            color: #4a5568;
            margin-top: 5px;
            text-align: justify;
        }
        .badge-list {
            margin-top: 10px;
            font-size: 12px;
        }
        .badge {
            display: inline-block;
            background-color: #edf2f7;
            color: #4a5568;
            padding: 3px 8px;
            border-radius: 4px;
            margin-right: 5px;
            margin-bottom: 5px;
            border: 1px solid #cbd5e0;
        }
        .skill-badge {
            background-color: #ebf8ff;
            color: #2b6cb0;
            border-color: #bee3f8;
        }
        .footer {
            margin-top: 40px;
            font-size: 10px;
            color: #a0aec0;
            text-align: center;
            border-top: 1px solid #edf2f7;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    @php
        $base64Image = null;
        if ($record->photo) {
            $path = public_path('storage/' . $record->photo); // Asumiendo disco public estándar
            
            // Si no está en storage, intenta buscarlo directo en public (casos heredados)
            if(!file_exists($path)) {
                $path = public_path($record->photo);
            }

            if (file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $base64Image = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }
    @endphp

    <div class="sidebar">
        @if($base64Image)
        <div class="profile-image-container">
            <img src="{{ $base64Image }}" alt="Fotografía" class="profile-image">
        </div>
        @endif

        <h2>Contacto</h2>
        <ul>
            <li><strong>Móvil:</strong> {{ $record->phone }}</li>
            <li><strong>Correo:</strong> {{ $record->email }}</li>
            <li><strong>Ubicación:</strong> {{ $record->city?->name }}, {{ $record->state?->name }}</li>
            <li><strong>Dirección:</strong> {{ $record->address }}</li>
        </ul>

        <h2>Documentación</h2>
        <ul>
            <li><strong>Tipo:</strong> {{ strtoupper($record->documentationtype) }}</li>
            <li><strong>Número:</strong> {{ $record->documentationnumber }}</li>
        </ul>

        <h2>Métricas HRIS</h2>
        <ul>
            <li><strong>Visibilidad Comités:</strong> {{ $record->resume?->is_available_for_committees ? 'SÍ (Disponible)' : 'NO' }}</li>
            <li><strong>Perfil Sistema:</strong> {{ $record->resume?->profile_score ?? 0 }} Pts.</li>
            <li><strong>Categoría:</strong> {{ $record->category?->name ?? 'N/A' }}</li>
            <li><strong>Tipo Voto:</strong> {{ $record->voter_type }}</li>
        </ul>

        @if($record->divipol)
        <h2>Lugar de Votación</h2>
        <ul>
            <li><strong>Puesto:</strong> {{ $record->divipol->nom_puesto }}</li>
            <li><strong>Mesa:</strong> {{ $record->mesa ?? 'Asignación Pdte.' }}</li>
            <li style="font-size:11px; margin-top:5px; color:#718096;">Dir: {{ $record->divipol->direccion }}</li>
        </ul>
        @endif
    </div>

    <div class="main-content">
        
        <h1 class="main-title">{{ $record->name }} {{ $record->lastname }}</h1>
        <h3 class="sub-title">{{ $record->profession ?? 'Líder Social y Comunitario' }}</h3>

        @if($record->resume?->profile_summary)
        <div class="content-section">
            <div class="section-header">Resumen de Perfil</div>
            <p class="item-desc">{{ $record->resume->profile_summary }}</p>
        </div>
        @endif

        @if($record->experience->count() > 0)
        <div class="content-section">
            <div class="section-header">Experiencia y Activismo</div>
            
            @foreach($record->experience->sortByDesc('start_date') as $exp)
            <div class="item-row">
                <div class="item-title">{{ $exp->position }}</div>
                <div class="item-subtitle">{{ $exp->company }} <span class="item-date"> | {{ $exp->start_date?->format('F Y') ?? '' }} - {{ $exp->currently_working ? 'Actualidad' : ($exp->end_date?->format('F Y') ?? '') }}</span></div>
                @if($exp->achievements)
                    <div class="item-desc">{{ $exp->achievements }}</div>
                @endif
            </div>
            @endforeach
        </div>
        @endif

        @if($record->education->count() > 0)
        <div class="content-section">
            <div class="section-header">Formación Académica</div>
            
            @foreach($record->education->sortByDesc('start_date') as $edu)
            <div class="item-row">
                <div class="item-title">{{ $edu->degree }}</div>
                <div class="item-subtitle">{{ $edu->institution }} <span class="item-date"> | {{ $edu->start_date?->format('Y') ?? '' }} - {{ $edu->currently_studying ? 'Presente' : ($edu->end_date?->format('Y') ?? '') }} ({{ $edu->status }})</span></div>
            </div>
            @endforeach
        </div>
        @endif

        @if($record->skills->count() > 0)
        <div class="content-section">
            <div class="section-header">Habilidades y Destrezas</div>
            <div class="badge-list">
                @foreach($record->skills as $skill)
                    <span class="badge skill-badge">
                        {{ $skill->name }} ({{ current(explode(' ', $skill->pivot->level)) }})
                    </span>
                @endforeach
            </div>
        </div>
        @endif

        @if($record->politicalCommittees->count() > 0)
        <div class="content-section">
            <div class="section-header">Comités Asignados</div>
            <div class="badge-list">
                @foreach($record->politicalCommittees as $committee)
                    <span class="badge">
                        🗳️ {{ $committee->name }} | {{ $committee->pivot->role }}
                    </span>
                @endforeach
            </div>
        </div>
        @endif

        <div class="footer">
            Documento privado y confidencial. Generado por HRIS Demosol el {{ now()->format('d/m/Y \a \l\a\s H:i') }}.<br>
            Tratamiento de datos autorizado según Ley Estatutaria 1581 de 2012 (Habeas Data).
        </div>

    </div>

</body>
</html>
