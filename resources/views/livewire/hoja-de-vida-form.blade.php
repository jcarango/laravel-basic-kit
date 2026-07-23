<div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Radicar Hoja de Vida</h2>
        <p class="text-gray-600 text-sm mt-2">Diligencia tus datos y adjunta tu hoja de vida en formato PDF.</p>
    </div>

    @if($successMessage)
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            <span class="block sm:inline">{{ $successMessage }}</span>
        </div>
    @endif

    @if($errorMessage)
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            <span class="block sm:inline">{{ $errorMessage }}</span>
        </div>
    @endif

    <form wire:submit.prevent="submit">
        <!-- Identificación -->
        <div class="mb-4">
            <label for="identificacion" class="block text-sm font-medium text-gray-700">Cédula o Correo Electrónico</label>
            <input type="text" id="identificacion" wire:model.defer="identificacion" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border" placeholder="Digita tu número de documento o correo">
            @error('identificacion') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- Perfil Profesional -->
        <div class="mb-4">
            <label for="perfil_profesional" class="block text-sm font-medium text-gray-700">Perfil Profesional</label>
            <textarea id="perfil_profesional" wire:model.defer="perfil_profesional" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border" placeholder="Breve descripción de tu perfil profesional, habilidades y propósito..."></textarea>
            @error('perfil_profesional') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- Tipo de Ingreso -->
        <div class="mb-4 p-4 border rounded-md bg-gray-50">
            <label class="block text-sm font-medium text-gray-700 mb-2">¿Cómo deseas registrar el resto de tu información?</label>
            <div class="flex items-center space-x-6">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="radio" wire:model.live="tipo_ingreso" value="archivo" class="form-radio text-indigo-600 h-4 w-4">
                    <span class="ml-2 text-sm text-gray-700">Adjuntar Archivo PDF</span>
                </label>
                <label class="inline-flex items-center cursor-pointer">
                    <input type="radio" wire:model.live="tipo_ingreso" value="formulario" class="form-radio text-indigo-600 h-4 w-4">
                    <span class="ml-2 text-sm text-gray-700">Diligenciar Formulario</span>
                </label>
            </div>
        </div>

        @if($tipo_ingreso === 'archivo')
            <!-- Archivo PDF -->
            <div class="mb-4">
                <label for="archivo_pdf" class="block text-sm font-medium text-gray-700">Hoja de Vida (Solo PDF, máx. 20MB)</label>
                <input type="file" id="archivo_pdf" wire:model="archivo_pdf" accept="application/pdf" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-300 rounded-md">
                @error('archivo_pdf') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                
                <div wire:loading wire:target="archivo_pdf" class="mt-2 text-sm text-indigo-600">
                    Subiendo archivo...
                </div>
            </div>
        @else
            <!-- Manual Entry -->
            <div class="mb-4">
                <label for="experiencia" class="block text-sm font-medium text-gray-700">Experiencia Laboral</label>
                <textarea id="experiencia" wire:model.defer="experiencia" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border" placeholder="Detalla tu experiencia profesional, cargos ocupados y fechas..."></textarea>
                @error('experiencia') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label for="educacion" class="block text-sm font-medium text-gray-700">Educación y Formación</label>
                <textarea id="educacion" wire:model.defer="educacion" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border" placeholder="Detalla tus estudios, instituciones y títulos obtenidos..."></textarea>
                @error('educacion') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label for="referencias" class="block text-sm font-medium text-gray-700">Referencias (Personales o Laborales)</label>
                <textarea id="referencias" wire:model.defer="referencias" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border" placeholder="Nombres, relación y teléfonos de contacto..."></textarea>
                @error('referencias') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
        @endif

        <!-- Habeas Data -->
        <div class="mb-6 flex flex-col">
            <div class="flex items-start">
                <div class="flex h-5 items-center">
                    <input id="habeas_data_accepted" type="checkbox" wire:model.defer="habeas_data_accepted" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                </div>
                <div class="ml-3 text-sm">
                    <label for="habeas_data_accepted" class="font-medium text-gray-700">Acepto la Política de Tratamiento de Datos (Habeas Data)</label>
                    <p class="text-gray-500 text-xs mt-1">Autorizo el almacenamiento y uso de mis datos personales de acuerdo con la <a href="/habeas-data" target="_blank" class="text-indigo-600 hover:text-indigo-800 underline">Ley 1581 de Habeas Data</a>, exclusivamente para fines de la organización y procesos de registro.</p>
                </div>
            </div>
            @error('habeas_data_accepted') <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span> @enderror
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end">
            <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                <span wire:loading.remove wire:target="submit">Radicar Hoja de Vida</span>
                <span wire:loading wire:target="submit">Enviando...</span>
            </button>
        </div>
    </form>
</div>
