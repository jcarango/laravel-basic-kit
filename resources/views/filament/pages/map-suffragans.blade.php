<x-filament-panels::page>
    <div wire:ignore x-data="mapSuffragansPage()" x-init="initMap()" class="relative w-full" style="min-height: 80vh;">
        
        <!-- Filtros Flotantes -->
        <div style="position: absolute; top: 10px; right: 10px; z-index: 1000; background: white; padding: 15px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 250px;">
            <h4 style="margin: 0 0 10px; font-weight: bold; font-size: 16px;">Filtros por Roles</h4>
            <label style="font-size: 14px; margin-bottom: 5px; display: block;">Escoge un Rol:</label>
            <select x-model="selectedColor" @change="applyFilter()" style="margin-bottom: 10px; width: 100%; padding: 5px; border-radius: 4px; border: 1px solid #ccc;">
                <option value="all">Todos los Roles</option>
                <option value="purple">Nodo Multiplicador</option>
                <option value="red">Líderes</option>
                <option value="yellow">Testigo Electoral</option>
                <option value="green">Militantes</option>
            </select>
            <p style="margin: 0; font-size: 14px;"><strong>Puntos visibles:</strong> <span x-text="visibleCount"></span></p>
        </div>

        <!-- Contenedor del Mapa -->
        <div x-ref="mapContainer" style="height: 100%; width: 100%; min-height: 80vh; z-index: 10;"></div>
    </div>
</x-filament-panels::page>

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.fullscreen/1.6.0/Control.FullScreen.min.css" />
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.fullscreen/1.6.0/Control.FullScreen.min.js"></script>
    <script>
        function mapSuffragansPage() {
            return {
                map: null,
                allPoints: @js($this->getPoints()),
                markersLayer: null,
                selectedColor: 'all',
                visibleCount: 0,
                
                initMap() {
                    if (typeof L === 'undefined') {
                        setTimeout(() => this.initMap(), 100);
                        return;
                    }

                    if (this.map !== null) {
                        this.map.remove();
                    }

                    this.map = L.map(this.$refs.mapContainer, {
                        fullscreenControl: true
                    }).setView([6.2442, -75.5812], 12);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(this.map);

                    this.markersLayer = L.layerGroup().addTo(this.map);
                    this.renderMarkers(this.allPoints);
                },

                applyFilter() {
                    let filtered = this.selectedColor === 'all' 
                        ? this.allPoints 
                        : this.allPoints.filter(p => p.color === this.selectedColor);
                    
                    this.renderMarkers(filtered);
                },

                renderMarkers(points) {
                    console.log("MapSuffragans: Rendering " + points.length + " markers.");
                    this.markersLayer.clearLayers();
                    let renderedMarkers = [];
                    
                    points.forEach(point => {
                        if (point.latitude && point.longitude) {
                            let marker = L.circleMarker([point.latitude, point.longitude], {
                                radius: 8,
                                fillColor: point.color,
                                color: '#000',
                                weight: 1,
                                opacity: 1,
                                fillOpacity: 0.8
                            });

                            let photoUrl = point.photo ? `/storage/${point.photo}` : '/img/default-avatar.png';
                            
                            marker.bindPopup(`
                                <div style="text-align:center;">
                                    <div style="width: 80px; height: 80px; border-radius: 50%; border: 2px solid #ccc; margin: 0 auto 10px auto; overflow: hidden; display: flex; align-items: center; justify-content: center; background-color: #f3f4f6;">
                                        <img src="${photoUrl}" alt="${point.name}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=\\\'http://www.w3.org/2000/svg\\\' fill=\\\'none\\\' viewBox=\\\'0 0 24 24\\\' stroke=\\\'currentColor\\\'><path stroke-linecap=\\\'round\\\' stroke-linejoin=\\\'round\\\' stroke-width=\\\'2\\\' d=\\\'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z\\\' /></svg>'">
                                    </div>
                                    <strong>${point.name} ${point.lastname}</strong>
                                </div>
                                <div style="font-size: 13px; margin-top: 10px;">
                                    <strong>Cédula: </strong>${point.documentationnumber}<br>
                                    <strong>Correo: </strong><a href="mailto:${point.email}">${point.email || 'N/A'}</a><br>
                                    <strong>WhatsApp: </strong><a href="https://wa.me/57${String(point.phone).replace(/\\D/g,'')}" target="_blank">${point.phone}</a><br>
                                </div>
                                <hr style="margin: 8px 0;">
                                <strong>Categoría: </strong>${point.category}
                            `);

                            this.markersLayer.addLayer(marker);
                            renderedMarkers.push(marker);
                        }
                    });

                    this.visibleCount = renderedMarkers.length;

                    if (renderedMarkers.length > 0) {
                        let group = new L.featureGroup(renderedMarkers);
                        this.map.fitBounds(group.getBounds().pad(0.1));
                    }
                }
            }
        }
    </script>
@endpush