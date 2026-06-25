@props(['latitude', 'longitude'])

<div
    x-data="{
        initMap() {
            if (typeof L !== 'undefined') {
                this.createMap();
                return;
            }
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
            document.head.appendChild(link);
            const script = document.createElement('script');
            script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
            script.onload = () => this.createMap();
            document.head.appendChild(script);
        },
        createMap() {
            const map = L.map(this.$refs.mapEl).setView([{{ $latitude }}, {{ $longitude }}], 13);
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);
            L.marker([{{ $latitude }}, {{ $longitude }}]).addTo(map);
        }
    }"
    x-init="initMap()"
    class="block"
>
    <div x-ref="mapEl" style="height: 300px; width: 100%; border-radius: 4px;"></div>
</div>
