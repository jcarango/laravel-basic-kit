<div class="flex flex-col items-center justify-center p-6 space-y-4 text-center">
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(250)->generate($record->documentationnumber) !!}
    </div>
    
    <div>
        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
            {{ $record->name }} {{ $record->lastname }}
        </h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            CC: {{ $record->documentationnumber }}
        </p>
    </div>

    <div class="text-xs text-gray-400 mt-4">
        Escanee este código el Día D para registrar la asistencia.
    </div>
</div>
