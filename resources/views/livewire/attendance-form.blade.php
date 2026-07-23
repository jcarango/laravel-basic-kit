<div>
    @if ($successMessage)
        <div class="alert alert-success mt-3" role="alert">
            ¡Asistencia registrada correctamente! Puedes realizar otro registro.
        </div>
    @endif

    <form wire:submit.prevent="submit" class="needs-validation" novalidate>
        <div class="row g-3">
            <!-- Sección Personal -->
            <div class="col-12">
                <h5 class="border-bottom pb-2">Información Personal</h5>
            </div>
            <div class="col-12 mb-3">
                <label class="form-label text-primary fw-bold">¿Líder que te invitó al evento?</label>
                <select class="form-select border-primary" wire:model="leader_id">
                    <option value="">Ninguno / Soy Independiente</option>
                    @foreach($leaders as $lider)
                        <option value="{{ $lider->id }}">{{ $lider->name }} {{ $lider->lastname }}</option>
                    @endforeach
                </select>
                @error('leader_id') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>
            <div class="col-md-6 border-end">
                <div class="mb-3">
                    <label class="form-label">Nombre *</label>
                    <input type="text" class="form-control" wire:model="name" required>
                    @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Apellido *</label>
                    <input type="text" class="form-control" wire:model="lastname" required>
                    @error('lastname') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Teléfono *</label>
                    <input type="text" class="form-control" wire:model="phone" required>
                    @error('phone') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Correo Electrónico</label>
                    <input type="email" class="form-control" wire:model="email">
                    @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Sección Documento -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Tipo de Documento *</label>
                    <select class="form-select" wire:model="documentationtype" required>
                        <option value="CC">Cédula de Ciudadanía</option>
                        <option value="TI">Tarjeta de Identidad</option>
                        <option value="CE">Cédula de Extranjería</option>
                        <option value="PAS">Pasaporte</option>
                    </select>
                    @error('documentationtype') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Número de Documento *</label>
                    <input type="text" class="form-control" wire:model="documentationnumber" required>
                    @error('documentationnumber') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Profesión u Oficio</label>
                    <input type="text" class="form-control" wire:model="profession">
                    @error('profession') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Intención de Voto</label>
                    <select class="form-select" wire:model="voter_type">
                        <option value="Opinión">Voto de Opinión</option>
                        <option value="Blando">Voto Blando (Dudoso)</option>
                        <option value="Duro">Voto Duro (Seguro)</option>
                    </select>
                </div>
            </div>

            <!-- Sección Ubicación -->
            <div class="col-12 mt-4">
                <h5 class="border-bottom pb-2">Ubicación y Puesto de Votación</h5>
            </div>
            <div class="col-md-6 border-end">
                <div class="mb-3">
                    <label class="form-label">País</label>
                    <select class="form-select" wire:model.live="country_id">
                        <option value="">Seleccione País</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Departamento</label>
                    <select class="form-select" wire:model.live="state_id" {{ empty($states) ? 'disabled' : '' }}>
                        <option value="">Seleccione Departamento</option>
                        @foreach($states as $state)
                            <option value="{{ $state->id }}">{{ $state->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ciudad</label>
                    <select class="form-select" wire:model="city_id" {{ empty($cities) ? 'disabled' : '' }}>
                        <option value="">Seleccione Ciudad</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Dirección / Barrio</label>
                    <input type="text" class="form-control" wire:model="address">
                    @error('address') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Puesto de Votación</label>
                    <select class="form-select" wire:model="divipol_id">
                        <option value="">Seleccione Puesto Político</option>
                        @foreach($divipols as $divipol)
                            <option value="{{ $divipol->id }}">{{ $divipol->nom_puesto }} ({{ $divipol->municipio }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Redes Sociales -->
            <div class="col-12 mt-4">
                <h5 class="border-bottom pb-2">Redes Sociales (Opcional)</h5>
            </div>
            <div class="col-md-3">
                <label class="form-label">Facebook</label>
                <input type="text" class="form-control form-control-sm" wire:model="facebook" placeholder="Usuario / Enlace">
            </div>
            <div class="col-md-3">
                <label class="form-label">Instagram</label>
                <input type="text" class="form-control form-control-sm" wire:model="instagram" placeholder="Usuario / Enlace">
            </div>
            <div class="col-md-3">
                <label class="form-label">Twitter/X</label>
                <input type="text" class="form-control form-control-sm" wire:model="twitter" placeholder="Usuario / Enlace">
            </div>
            <div class="col-md-3">
                <label class="form-label">LinkedIn</label>
                <input type="text" class="form-control form-control-sm" wire:model="linkedin" placeholder="Usuario / Enlace">
            </div>

            <!-- Submit -->
            <div class="col-12 text-center mt-5">
                <button type="submit" class="btn btn-primary btn-lg w-50" wire:loading.attr="disabled">
                    <span wire:loading.remove>Registrar Asistencia</span>
                    <span wire:loading>Registrando...</span>
                </button>
            </div>
        </div>
    </form>

    <script>
        document.addEventListener('livewire:init', () => {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    @this.set('latitude_event', position.coords.latitude);
                    @this.set('longitude_event', position.coords.longitude);
                }, function (error) {
                    console.error("Geolocación no disponible");
                });
            }
        });
    </script>
</div>
