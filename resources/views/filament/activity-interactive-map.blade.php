<div
    x-data="{
        lat: {{ $lat }},
        lng: {{ $lng }},
        address: '',
        map: null,
        marker: null,
        loading: false,
        error: '',
        init() {
            if (typeof L !== 'undefined') {
                this.$nextTick(() => this.createMap());
                return;
            }
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
            document.head.appendChild(link);
            const script = document.createElement('script');
            script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
            script.onload = () => this.$nextTick(() => this.createMap());
            document.head.appendChild(script);
        },
        createMap() {
            this.map = L.map(this.$refs.mapEl).setView([this.lat, this.lng], {{ $zoom }});
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(this.map);
            this.marker = L.marker([this.lat, this.lng], { draggable: true }).addTo(this.map);
            this.marker.on('dragend', (e) => {
                const pos = e.target.getLatLng();
                this.lat = Math.round(pos.lat * 1000000) / 1000000;
                this.lng = Math.round(pos.lng * 1000000) / 1000000;
                this.$wire.set('data.latitude', this.lat);
                this.$wire.set('data.longitude', this.lng);
            });
        },
        async geocode() {
            if (!this.address.trim()) return;
            this.loading = true;
            this.error = '';
            try {
                const res = await fetch(
                    'https://nominatim.openstreetmap.org/search?q=' +
                    encodeURIComponent(this.address) + '&format=json&limit=1',
                    { headers: { 'Accept': 'application/json' } }
                );
                const data = await res.json();
                if (!data.length) { this.error = 'Indirizzo non trovato'; return; }
                this.lat = Math.round(parseFloat(data[0].lat) * 1000000) / 1000000;
                this.lng = Math.round(parseFloat(data[0].lon) * 1000000) / 1000000;
                this.map.setView([this.lat, this.lng], 14);
                this.marker.setLatLng([this.lat, this.lng]);
                this.$wire.set('data.latitude', this.lat);
                this.$wire.set('data.longitude', this.lng);
            } catch(e) {
                this.error = 'Errore durante la geocodifica';
            } finally {
                this.loading = false;
            }
        }
    }"
    x-init="init()"
>
    <div class="flex gap-2 mb-3">
        <input
            type="text"
            x-model="address"
            @keydown.enter.prevent="geocode()"
            placeholder="Cerca indirizzo o luogo di ritrovo..."
            class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
        >
        <button
            type="button"
            @click="geocode()"
            :disabled="loading"
            class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700 disabled:opacity-50 transition"
        >
            <span x-show="!loading">Cerca</span>
            <span x-show="loading">…</span>
        </button>
    </div>

    <p x-show="error" x-text="error" class="text-sm text-red-600 mb-2"></p>

    <div x-ref="mapEl" style="height:440px;width:100%;border-radius:8px;border:1px solid #d1d5db;"></div>

    <div class="mt-3 flex gap-6 text-sm text-gray-500 dark:text-gray-400">
        <span>Latitudine: <strong class="text-gray-800 dark:text-gray-200" x-text="lat"></strong></span>
        <span>Longitudine: <strong class="text-gray-800 dark:text-gray-200" x-text="lng"></strong></span>
        <span class="text-xs italic">Trascina il pin o cerca un indirizzo per aggiornare la posizione</span>
    </div>
</div>
