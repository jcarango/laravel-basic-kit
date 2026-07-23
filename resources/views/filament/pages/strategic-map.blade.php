<x-filament-panels::page>
<div 
    wire:ignore
    x-data="strategicMap()"
    x-init="init()"
    class="relative w-full"
    style="height: 85vh;"
>

    <!-- PANEL -->
    <div class="absolute top-4 right-4 z-[1000] bg-white p-4 rounded-xl shadow-xl w-72">
        <h2 class="font-bold text-lg mb-3">Centro Estratégico</h2>

        <select x-model="category" @change="reload()"
            class="w-full border rounded p-2 text-sm mb-2">
            <option value="all">Todos</option>
            <option value="Líderes">Líderes</option>
            <option value="Nodo Multiplicador">Nodo</option>
            <option value="Testigo Electoral">Testigo</option>
            <option value="Militantes">Militantes</option>
        </select>

        <button @click="toggleHeat()" 
            class="w-full bg-black text-white rounded p-2 text-sm">
            Toggle Heatmap
        </button>

        <div class="mt-3 text-sm">
            Puntos visibles:
            <strong x-text="visibleCount"></strong>
        </div>
    </div>

    <div x-ref="map" class="w-full h-full rounded-xl"></div>
</div>

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>
<script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>
<script>
function strategicMap() {
    return {
        map: null,
        cluster: null,
        heatLayer: null,
        geojsonLayer: null,
        heatEnabled: false,
        visibleCount: 0,
        category: 'all',
        currentPoints: [],
        comunaCounts: {},

        async init() {
            if (typeof L === 'undefined') {
                setTimeout(() => this.init(), 100);
                return;
            }

            this.map = L.map(this.$refs.map).setView([6.2442, -75.5812], 12)

            L.tileLayer(
                'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                { attribution: '© OpenStreetMap' }
            ).addTo(this.map)

            this.cluster = L.markerClusterGroup()
            this.map.addLayer(this.cluster)

            this.map.on('moveend', () => this.reload())

            this.reload()
            this.loadPolygons()
        },

        async loadPolygons() {
            try {
                this.comunaCounts = await this.$wire.getComunaCounts()

                const response = await fetch('/geojson/medellin-comunas.geojson')
                if (!response.ok) throw new Error('GeoJSON not found');
                
                const geojson = await response.json()

                this.geojsonLayer = L.geoJSON(geojson, {
                    style: (feature) => {
                        let name = feature.properties.nombre
                        let total = this.comunaCounts[name] ?? 0

                        return {
                            fillColor: total > 200 ? '#b91c1c' :
                                       total > 100 ? '#f97316' :
                                       total > 50 ? '#facc15' :
                                       '#22c55e',
                            weight: 1,
                            color: '#333',
                            fillOpacity: 0.4
                        }
                    },
                    onEachFeature: (feature, layer) => {
                        let name = feature.properties.nombre
                        let total = this.comunaCounts[name] ?? 0

                        layer.bindPopup(`
                            <strong>${name}</strong><br>
                            Total sufragantes: ${total}
                        `)
                    }
                }).addTo(this.map)
            } catch (e) {
                console.warn("GeoJSON loading failed. Polygons will not be rendered.", e);
            }
        },

        async reload() {
            const bounds = this.map.getBounds();
            console.log("StrategicMap: Bounds changed. Fetching points...", bounds);
            const data = await this.$wire.getPointsByBounds(
                bounds.getNorth(),
                bounds.getSouth(),
                bounds.getEast(),
                bounds.getWest(),
                this.category
            )

            console.log("StrategicMap: Received points: ", data.length);
            this.currentPoints = data;
            this.render();
        },

        render() {
            console.log("StrategicMap: Rendering layer (Heatmap: " + this.heatEnabled + ")...");
            this.cluster.clearLayers()
            if (this.heatLayer) {
                this.map.removeLayer(this.heatLayer)
            }

            let markers = []

            this.currentPoints.forEach(p => {
                if (!p.latitude || !p.longitude) return

                let marker = L.circleMarker(
                    [p.latitude, p.longitude],
                    {
                        radius: 7,
                        fillColor: p.color,
                        color: '#000',
                        weight: 1,
                        fillOpacity: 0.85
                    }
                )

                let photoUrl = p.photo ? `/storage/${p.photo}` : '/img/default-avatar.png';
                let phoneFormatted = String(p.phone || '').replace(/\D/g,'');

                let popupHtml = `
                    <div style="text-align:center;">
                        <div style="width: 80px; height: 80px; border-radius: 50%; border: 2px solid #ccc; margin: 0 auto 10px auto; overflow: hidden; display: flex; align-items: center; justify-content: center; background-color: #f3f4f6;">
                            <img src="${photoUrl}" alt="${p.name}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=\\\'http://www.w3.org/2000/svg\\\' fill=\\\'none\\\' viewBox=\\\'0 0 24 24\\\' stroke=\\\'currentColor\\\'><path stroke-linecap=\\\'round\\\' stroke-linejoin=\\\'round\\\' stroke-width=\\\'2\\\' d=\\\'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z\\\' /></svg>'">
                        </div>
                        <strong>${p.name} ${p.lastname}</strong>
                    </div>
                    <div style="font-size: 13px; margin-top: 10px;">
                        <strong>Cédula: </strong>${p.documentationnumber || 'N/A'}<br>
                        <strong>Correo: </strong><a href="mailto:${p.email}">${p.email || 'N/A'}</a><br>
                        <strong>WhatsApp: </strong><a href="https://wa.me/57${phoneFormatted}" target="_blank">${p.phone || 'N/A'}</a><br>
                    </div>
                    <hr style="margin: 8px 0;">
                    <strong>Categoría: </strong>${p.category}
                `;

                marker.bindPopup(popupHtml);

                this.cluster.addLayer(marker)
                markers.push(marker)
            })

            this.visibleCount = markers.length

            if (this.heatEnabled) {
                let heatData = this.currentPoints.map(p => [
                    p.latitude,
                    p.longitude,
                    1
                ])

                this.heatLayer = L.heatLayer(heatData, {
                    radius: 25
                }).addTo(this.map)

                this.cluster.clearLayers()
            }
        },

        toggleHeat() {
            this.heatEnabled = !this.heatEnabled
            this.render()
        }
    }
}
</script>
@endpush
</x-filament-panels::page>