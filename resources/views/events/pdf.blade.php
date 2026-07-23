<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Evento: {{ $event->name }}</title>
    <style>
        @page { margin: 1cm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.4; font-size: 13px; margin: 0; padding: 0; }
        .container { max-width: 800px; margin: 0 auto; border: 1px solid #eee; padding: 25px; border-radius: 8px; }
        
        .header { border-bottom: 2px solid {{ $event->color }}; padding-bottom: 10px; margin-bottom: 15px; display: table; width: 100%; }
        .header-logo { display: table-cell; vertical-align: middle; width: 80px; }
        .header-text { display: table-cell; vertical-align: middle; padding-left: 15px; }
        .campaign-name { font-size: 12px; font-weight: bold; color: #666; text-transform: uppercase; }
        .event-title { font-size: 22px; font-weight: bold; margin: 2px 0; color: #111; }
        
        .main-content { display: table; width: 100%; margin-top: 10px; }
        .details-col { display: table-cell; width: 65%; vertical-align: top; }
        .candidate-col { display: table-cell; width: 35%; vertical-align: top; text-align: center; border-left: 1px solid #eee; padding-left: 15px; }
        
        .section-title { font-size: 12px; color: {{ $event->color }}; font-weight: bold; margin-bottom: 8px; border-bottom: 1px solid #eee; padding-bottom: 3px; text-transform: uppercase; }
        .detail-item { margin-bottom: 10px; }
        .detail-label { font-size: 10px; color: #888; font-weight: bold; display: block; text-transform: uppercase; }
        .detail-value { font-size: 13px; font-weight: 500; }
        
        .candidate-photo { width: 90px; height: 90px; border-radius: 50%; object-fit: cover; border: 2px solid #eee; margin-bottom: 5px; }
        .candidate-name { font-size: 14px; font-weight: bold; margin: 2px 0; }
        .candidate-role { font-size: 11px; color: #666; margin: 0; }
        
        .qr-section { text-align: center; margin-top: 20px; padding: 15px; background-color: #f9f9f9; border-radius: 10px; }
        .qr-instruction { font-size: 12px; font-weight: bold; margin-bottom: 10px; color: {{ $event->color }}; }
        .qr-image { border: 6px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); width: 140px; }
        
        .footer { margin-top: 20px; text-align: center; font-size: 10px; color: #aaa; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Encabezado con información de Campaña -->
        <div class="header">
            @php
                $candidate = $event->candidate;
                $campain = $candidate?->campains; 
            @endphp
            
            <div class="header-logo">
                @if($campain && $campain->logo)
                    <img src="{{ public_path('storage/' . $campain->logo) }}" width="80" alt="Logo">
                @else
                    <div style="width: 80px; height: 80px; background: #eee; border-radius: 6px;"></div>
                @endif
            </div>
            
            <div class="header-text">
                <div class="campaign-name">{{ $campain ? $campain->name : 'Campaña Política' }}</div>
                <div class="event-title">{{ $event->name }}</div>
            </div>
        </div>

        <div class="main-content">
            <!-- Columna de Detalles -->
            <div class="details-col">
                <div class="section-title">Información del Evento</div>
                
                <div class="detail-item">
                    <span class="detail-label">FECHA Y HORA</span>
                    <span class="detail-value">
                        {{ $event->starts_at->translatedFormat('l, d \d\e F Y') }} <br>
                        {{ $event->starts_at->format('H:i') }} - {{ $event->ends_at->format('H:i') }}
                    </span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">RESUMEN DE LA REUNIÓN</span>
                    <span class="detail-value">{{ $event->description }}</span>
                </div>

                @if($event->leader)
                <div class="detail-item">
                    <span class="detail-label">LÍDER RESPONSABLE</span>
                    <span class="detail-value">{{ $event->leader->name }} {{ $event->leader->lastname }}</span>
                </div>
                @endif
            </div>

            <!-- Columna de Candidato -->
            <div class="candidate-col">
                <div class="section-title">En Representación De</div>
                
                @if($candidate)
                    @if($candidate->photo)
                        <img src="{{ public_path('storage/' . $candidate->photo) }}" class="candidate-photo" alt="Candidato">
                    @else
                        <div style="width: 90px; height: 90px; background: #eee; border-radius: 50%; margin: 0 auto 5px;"></div>
                    @endif
                    <p class="candidate-name">{{ $candidate->name }} {{ $candidate->lastname }}</p>
                    <p class="candidate-role">Tu Candidato</p>
                @else
                    <p style="color: #888; font-size: 11px; padding-top: 20px;">Sin candidato <br> asignado</p>
                @endif
            </div>
        </div>

        <!-- Sección QR Reducida -->
        <div class="qr-section">
            <div class="qr-instruction">MARCA TU ASISTENCIA AQUÍ</div>
            <img src="{{ $qrCode }}" class="qr-image" alt="QR">
            <p style="margin-top: 8px; font-size: 9px; color: #888;">{{ url('/attendance/' . $event->id) }}</p>
        </div>

        <div class="footer">
            <p>Generado automáticamente por el portal DEMOSOL - {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>
</body>
</html>