<div class="max-w-3xl mx-auto py-10 px-4">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-600 to-blue-600 p-8 text-white">
            <h1 class="text-2xl font-bold">{{ $survey->title }}</h1>
            @if($survey->description)
                <p class="text-indigo-100 mt-2 text-sm">{{ $survey->description }}</p>
            @endif
        </div>

        <div class="p-8">
            @if($submitted)
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-800">¡Muchas Gracias!</h2>
                    <p class="text-slate-500 mt-2">Tus respuestas han sido registradas exitosamente en el sistema DEMOSOL.</p>
                </div>
            @else
                <form wire:submit.prevent="submit" class="space-y-6" x-data="{
                    init() {
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition((pos) => {
                                $wire.set('latitude', pos.coords.latitude);
                                $wire.set('longitude', pos.coords.longitude);
                            }, (err) => {
                                console.log('GPS Error:', err);
                            });
                        }
                    }
                }">
                    <div class="bg-slate-50 p-5 rounded-xl border border-slate-200 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                            <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wide">1. Datos Personales del Encuestado</h3>
                            <template x-if="$wire.latitude">
                                <span class="inline-flex items-center gap-1 bg-emerald-100 text-emerald-700 text-xs font-semibold px-2.5 py-1 rounded-full">
                                    📍 GPS Capturado
                                </span>
                            </template>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Nombre Completo *</label>
                                <input type="text" wire:model="respondent_name" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Ej: Maria Perez">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Cédula / Documento *</label>
                                <input type="text" wire:model="document_number" required class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Número de documento">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Teléfono / Celular</label>
                                <input type="tel" wire:model="phone" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Número de celular">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Correo Electrónico</label>
                                <input type="email" wire:model="email" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="correo@ejemplo.com">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Dirección de Residencia</label>
                                <input type="text" wire:model="address" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Dirección completa">
                            </div>
                        </div>
                    </div>


                    @foreach($survey->questions as $index => $q)
                        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
                            <label class="block font-medium text-slate-800 mb-2">
                                <span class="text-indigo-600 font-bold me-1">{{ $index + 1 }}.</span> {{ $q->question_text }}
                                @if($q->is_required)
                                    <span class="text-rose-500">*</span>
                                @endif
                            </label>

                            @if($q->type === 'text')
                                <textarea wire:model="answers.{{ $q->id }}" rows="2" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Escribe tu respuesta..."></textarea>
                            @elseif($q->type === 'number')
                                <input type="number" wire:model="answers.{{ $q->id }}" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @elseif($q->type === 'date')
                                <input type="date" wire:model="answers.{{ $q->id }}" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @elseif($q->type === 'boolean')
                                <div class="flex gap-6 mt-2">
                                    <label class="flex items-center gap-2 cursor-pointer text-sm text-slate-700">
                                        <input type="radio" value="Sí" wire:model="answers.{{ $q->id }}" class="text-indigo-600 focus:ring-indigo-500"> Sí
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer text-sm text-slate-700">
                                        <input type="radio" value="No" wire:model="answers.{{ $q->id }}" class="text-indigo-600 focus:ring-indigo-500"> No
                                    </label>
                                </div>
                            @elseif($q->type === 'scale')
                                <div class="flex gap-4 mt-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <label class="flex flex-col items-center cursor-pointer">
                                            <input type="radio" value="{{ $i }}" wire:model="answers.{{ $q->id }}" class="text-indigo-600 focus:ring-indigo-500">
                                            <span class="text-xs mt-1 font-semibold text-slate-600">{{ $i }}</span>
                                        </label>
                                    @endfor
                                </div>
                            @elseif($q->type === 'single_choice' && is_array($q->options))
                                <div class="space-y-2 mt-2">
                                    @foreach($q->options as $opt)
                                        <label class="flex items-center gap-2 cursor-pointer text-sm text-slate-700">
                                            <input type="radio" value="{{ $opt }}" wire:model="answers.{{ $q->id }}" class="text-indigo-600 focus:ring-indigo-500"> {{ $opt }}
                                        </label>
                                    @endforeach
                                </div>
                            @elseif($q->type === 'multiple_choice' && is_array($q->options))
                                <div class="space-y-2 mt-2">
                                    @foreach($q->options as $opt)
                                        <label class="flex items-center gap-2 cursor-pointer text-sm text-slate-700">
                                            <input type="checkbox" value="{{ $opt }}" wire:model="answers.{{ $q->id }}" class="rounded text-indigo-600 focus:ring-indigo-500"> {{ $opt }}
                                        </label>
                                    @endforeach
                                </div>
                            @endif

                            @error("answers.{$q->id}")
                                <span class="text-xs text-rose-500 mt-1 block">Esta pregunta es obligatoria.</span>
                            @enderror
                        </div>
                    @endforeach

                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-xl shadow-lg transition-all">
                        Enviar Encuesta
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
