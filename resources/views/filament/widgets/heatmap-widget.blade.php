<x-filament-widgets::widget>
    <x-filament::section>
        <h2 class="text-lg sm:text-xl font-bold tracking-tight text-gray-950 dark:text-white">
            Mapa de Calor - Intención de Voto
        </h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
            Ubicación geográfica de los sufragantes registrados en el sistema.
        </p>

        <!-- Alpine JS Wrapper -->
        <div 
            wire:ignore
            data-locations="{{ json_encode($this->getLocations()) }}"
            x-data="{
                map: null,
                locations: [],
                
                init() {
                    // Start initialization
                    let dataStr = this.$el.dataset.locations;
                    this.locations = dataStr ? JSON.parse(dataStr) : [];
                    console.log('HeatmapWidget: Parsed ' + this.locations.length + ' points from dataset.');

                    // Dynamic CSS
                    if (!document.getElementById('leaflet-css-heatmap')) {
                        let link = document.createElement('link');
                        link.id = 'leaflet-css-heatmap';
                        link.rel = 'stylesheet';
                        link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                        document.head.appendChild(link);
                    }

                    // Dynamic JS
                    if (typeof L === 'undefined') {
                        let script = document.createElement('script');
                        script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                        document.head.appendChild(script);
                        script.onload = () => {
                            this.renderMap();
                        };
                    } else {
                        setTimeout(() => { this.renderMap(); }, 150);
                    }
                },

                renderMap() {
                    if (this.map !== null) {
                        this.map.remove();
                    }

                    this.map = L.map(this.$refs.mapContainer).setView([6.2442, -75.5812], 12);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '© OpenStreetMap'
                    }).addTo(this.map);

                    if (this.locations && this.locations.length > 0) {
                        let markers = [];
                        this.locations.forEach((loc) => {
                            if(loc.lat && loc.lng) {
                                let style = 'background-color:' + loc.color + ';width:20px;height:20px;display:block;left:-10px;top:-10px;position:relative;border-radius:50%;border:2px solid #fff;box-shadow:0 0 4px rgba(0,0,0,0.4);';

                                let customIcon = L.divIcon({
                                    className: 'custom-pin',
                                    iconAnchor: [0, 0],
                                    labelAnchor: [-6, 0],
                                    popupAnchor: [0, -10],
                                    html: '<span style=\'' + style + '\'></span>'
                                });

                                let marker = L.marker([loc.lat, loc.lng], {icon: customIcon})
                                    .bindPopup(loc.popup)
                                    .addTo(this.map);
                                markers.push(marker);
                            }
                        });

                        if (markers.length > 0) {
                            let group = new L.featureGroup(markers);
                            this.map.fitBounds(group.getBounds().pad(0.1));
                        }
                    }
                }
            }"
            class="relative w-full"
            style="height: 500px; border-radius: 0.5rem; z-index: 10;"
        >
            <div x-ref="mapContainer" class="w-full h-full rounded-xl"></div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
