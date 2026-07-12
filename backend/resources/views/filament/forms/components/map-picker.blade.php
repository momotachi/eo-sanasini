@push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush

<style>
    .eo-map-picker { height: 350px; border-radius: 0.5rem; overflow: hidden; border: 1px solid #e5e7eb; }
    .eo-map-picker .leaflet-map { height: 100%; width: 100%; }
    .eo-map-coords { font-family: ui-monospace, monospace; font-size: 11px; color: #6b7280; margin-top: 4px; }
</style>

@php
    // baca state dari field sibling: latitude, longitude, map_zoom
    $statePath = $getName();
    $latPath = '../latitude';
    $lngPath = '../longitude';
    $zoomPath = '../map_zoom';
    // Filament livewire state path
    $lat = $get($latPath);
    $lng = $get($lngPath);
    $zoom = $get($zoomPath) ?: 13;
    $hasCoords = $lat !== null && $lng !== null;
@endphp

<div class="eo-map-picker">
    <div id="map-{{ $statePath }}" class="leaflet-map"></div>
</div>
<div class="eo-map-coords">
    @if($hasCoords)
        Lat: {{ $lat }}, Lng: {{ $lng }} —
        <a href="https://www.google.com/maps/search/?api=1&query={{ $lat }},{{ $lng }}" target="_blank">Buka di Google Maps ↗</a>
    @else
        Belum ada titik. Klik peta untuk pilih lokasi.
    @endif
</div>

@script
<script>
(function () {
    const statePath = @js($statePath);
    const containerId = 'map-' + statePath;
    const latInitial = @js($lat);
    const lngInitial = @js($lng);
    const zoomInitial = @js($zoom) || 13;

    function init() {
        const el = document.getElementById(containerId);
        if (!el || el._leaflet_id) return;

        // center: kalau ada koord simpan, kalau tidak default Jakarta
        const center = (latInitial && lngInitial)
            ? [parseFloat(latInitial), parseFloat(lngInitial)]
            : [-6.2088, 106.8456];

        const map = L.map(containerId).setView(center, zoomInitial);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap',
            maxZoom: 19,
        }).addTo(map);

        let marker = null;
        if (latInitial && lngInitial) {
            marker = L.marker(center).addTo(map);
        }

        // klik untuk set titik
        map.on('click', function (e) {
            const { lat, lng } = e.latlng;
            if (marker) {
                marker.setLatLng([lat, lng]);
            } else {
                marker = L.marker([lat, lng]).addTo(map);
            }
            // update Filament state via Livewire
            @this.set(@js($latPath), lat);
            @this.set(@js($lngPath), lng);
            @this.set(@js($zoomPath), map.getZoom());
            // auto-fill map_url
            @this.set('../map_url', 'https://www.google.com/maps/search/?api=1&query=' + lat + ',' + lng);
        });

        // update zoom saat zoom berubah
        map.on('zoomend', function () {
            @this.set(@js($zoomPath), map.getZoom());
        });
    }

    // init setelah DOM ready
    if (document.readyState !== 'loading') {
        setTimeout(init, 50);
    } else {
        document.addEventListener('DOMContentLoaded', () => setTimeout(init, 50));
    }
})();
</script>
@endscript
