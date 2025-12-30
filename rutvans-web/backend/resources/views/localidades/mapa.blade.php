<!-- MAPA -->
<link href="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    .swal-toast {
        font-size: 14px;
        border-radius: 8px;
        padding: 10px;
        background-color: #007BFF;
        color: white;
    }
    .mapboxgl-popup {
        max-width: 300px;
        font-size: 14px;
        text-align: left;
        color: #333;
    }
    .mapboxgl-popup-content {
        padding: 15px;
    }
    .mapboxgl-marker {
        border-radius: 50%;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    }
    .mapboxgl-ctrl {
        border-radius: 12px!important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.16)!important;
        background: rgba(255,255,255,0.95)!important;
    }
    #map {
        background: linear-gradient(140deg, #e0e7ef 0%, #d0e0fc 100%);
    }
    #map-mode-switch .btn.active,
    #map-mode-switch .btn:active,
    #map-mode-switch .btn:focus {
        background: #007bff !important;
        color: white !important;
        border-color: #007bff !important;
    }
</style>

<div class="position-relative" style="width: 100%; height:500px;">
    <div id="map-mode-switch" class="position-absolute" style="top: 16px; left: 16px; z-index: 15;">
        <div class="btn-group btn-group-toggle shadow rounded-pill" role="group" aria-label="Switch Map Mode">
            <button id="switch-2d-btn" class="btn btn-light btn-sm active border border-primary" type="button" title="Vista 2D" style="border-radius: 16px 0 0 16px;">
                <i class="fas fa-map"></i>
            </button>
            <button id="switch-3d-btn" class="btn btn-light btn-sm border border-primary" type="button" title="Vista 3D" style="border-radius: 0 16px 16px 0;">
                <i class="fas fa-cube"></i>
            </button>
        </div>
    </div>
    <div id="map" class="border border-primary rounded-3 shadow-sm flex-grow-1"
         style="width: 100%; height: 500px; border-radius: 10px; box-shadow: 0 8px 32px 0 rgba(31,38,135,0.37);"></div>
</div>

<script src="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let is3D = false;
let map = null;
let mapLoaded = false;
let currentStyle3D = 'mapbox://styles/mapbox/satellite-streets-v12';
let currentStyle2D = 'mapbox://styles/mapbox/streets-v12';
let lastCenter = [-89.5133, 20.9256];
let lastZoom = 16.5;
let lastPitch = 45;
let lastBearing = -17.6;
let savedMarkers = [];
let currentMarker = null;

// Base de datos de comisarías de Yucatán para mejor detección
const yucatanComisarias = {
    'Kanachen': 'Maxcanú',
    'Kanachén': 'Maxcanú',
    'Chocholá': 'Kopomá', 
    'Samahil': 'Umán',
    'Texán de Palomeque': 'Hunucmá',
    'Santa Rosa': 'Maxcanú',
    'Xcunyá': 'Umán',
    'Sinanché': 'Hunucmá',
    'Tetiz': 'Tetiz',
    'Kinchil': 'Kinchil',
    'Kopomá': 'Kopomá',
    'Maxcanú': 'Maxcanú',
    'Umán': 'Umán',
    'Hunucmá': 'Hunucmá',
    'Nohpat': 'Maxcanú',
    'Xanila': 'Maxcanú',
    'San Antonio Hool': 'Kopomá',
    'Mucuyché': 'Maxcanú',
    'Chablekal': 'Mérida',
    'Dzityá': 'Mérida',
    'Komchén': 'Mérida',
    'Cholul': 'Mérida'
};

function getMunicipalityFromComisaria(localityName) {
    if (!localityName) return null;
    const normalize = (str) => str.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    const normalizedLocality = normalize(localityName);
    for (const [comisaria, municipio] of Object.entries(yucatanComisarias)) {
        const normalizedComisaria = normalize(comisaria);
        if (normalizedLocality.includes(normalizedComisaria) || 
            normalizedComisaria.includes(normalizedLocality)) {
            return municipio;
        }
    }
    return null;
}

function createMap({style, pitch, bearing, zoom, center}) {
    if (map) {
        map.remove();
        map = null;
    }
    map = new mapboxgl.Map({
        container: 'map',
        style: style,
        center: center,
        zoom: zoom,
        pitch: pitch,
        bearing: bearing,
        antialias: true
    });
    map.addControl(new mapboxgl.NavigationControl({ showCompass: true, showZoom: true }), 'top-right');
    map.addControl(new mapboxgl.FullscreenControl(), 'top-right');
    map.dragRotate.enable();
    map.touchZoomRotate.enableRotation();
    map.on('load', () => {
        mapLoaded = true;
        map.resize();
        if (is3D) {
            const layers = map.getStyle().layers;
            const labelLayerId = layers.find(
                layer => layer.type === 'symbol' && layer.layout['text-field']
            )?.id;
            map.addLayer(
                {
                    id: '3d-buildings',
                    source: 'composite',
                    'source-layer': 'building',
                    filter: ['==', 'extrude', 'true'],
                    type: 'fill-extrusion',
                    minzoom: 15,
                    paint: {
                        'fill-extrusion-color': [
                            'interpolate', ['linear'], ['get', 'height'],
                            0, "#d1d5db",
                            20, "#b2bec3",
                            50, "#636e72"
                        ],
                        'fill-extrusion-height': ['get', 'height'],
                        'fill-extrusion-base': ['get', 'min_height'],
                        'fill-extrusion-opacity': 0.85
                    }
                },
                labelLayerId
            );
        }
        for (const { marker } of savedMarkers) {
            marker.addTo(map);
        }
        if (currentMarker) {
            currentMarker.addTo(map);
        }
    });
    map.on('click', async function(e) {
        const coordinates = e.lngLat;
        if (currentMarker) currentMarker.remove();
        currentMarker = new mapboxgl.Marker({ color: '#007BFF' }).setLngLat(coordinates).addTo(map);

        flyToLocation(map, coordinates.lng, coordinates.lat);

        const loadingToast = Swal.fire({
            title: 'Consultando ubicación...',
            text: 'Obteniendo datos precisos con Geoapify',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        try {
            const results = await getLocationFromMultipleAPIs(coordinates.lng, coordinates.lat);
            const bestResult = getBestLocationResult(results);
            loadingToast.close();
            if (bestResult) {
                document.getElementById('longitude').value = coordinates.lng;
                document.getElementById('latitude').value = coordinates.lat;
                document.getElementById('locality').value = bestResult.locality;
                document.getElementById('street').value = bestResult.street;
                document.getElementById('postal_code').value = bestResult.postal_code;
                document.getElementById('municipality').value = bestResult.municipality;
                document.getElementById('state').value = bestResult.state;
                document.getElementById('country').value = bestResult.country;
                document.getElementById('locality_type').value = bestResult.locality_type;
                const displayText = `${bestResult.locality}${bestResult.municipality ? ', ' + bestResult.municipality : ''}${bestResult.state ? ', ' + bestResult.state : ''}${bestResult.country ? ', ' + bestResult.country : ''}`;
                if(document.getElementById('locality_display')) document.getElementById('locality_display').value = displayText;
                const apiUsed = results[0]?.api || 'Desconocida';
                Swal.fire({
                    icon: 'success',
                    title: 'Ubicación encontrada',
                    html: `
                        <div style="text-align: left;">
                            <strong>📍 Localidad:</strong> ${bestResult.locality}<br>
                            <strong>🏛️ Municipio:</strong> ${bestResult.municipality}<br>
                            <strong>📮 Código Postal:</strong> ${bestResult.postal_code}<br>
                            <strong>🗺️ Estado:</strong> ${bestResult.state}<br>
                            <small style="color: #666;">API: ${apiUsed}</small>
                        </div>
                    `,
                    timer: 4000,
                    timerProgressBar: true,
                    toast: true,
                    position: 'bottom-end',
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Ubicación no encontrada',
                    text: 'No se pudo obtener información de esta ubicación',
                    timer: 2000,
                    toast: true,
                    position: 'bottom-end',
                    showConfirmButton: false
                });
            }
        } catch (error) {
            loadingToast.close();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Hubo un problema al obtener la ubicación',
                timer: 2000,
                toast: true,
                position: 'bottom-end',
                showConfirmButton: false
            });
        }
    });
}

function flyToLocation(map, lng, lat) {
    map.flyTo({
        center: [lng, lat],
        zoom: map.getZoom(),
        bearing: map.getBearing(),
        pitch: map.getPitch(),
        speed: 1.4,
        curve: 1.1,
        easing: function (t) { return t; }
    });
}

async function getLocationFromGeoapify(lng, lat) {
    const apiKey = '1b268500dc844f61a822f0663bb76584';
    try {
        const url = `https://api.geoapify.com/v1/geocode/reverse?lat=${lat}&lon=${lng}&apiKey=${apiKey}&format=json`;
        const response = await fetch(url);
        const data = await response.json();
        if (data.results && data.results.length > 0) {
            const result = data.results[0];
            return {
                api: 'Geoapify',
                locality: result.city || result.village || result.town || result.locality || '',
                street: result.street || result.address_line1 || '',
                municipality: result.county || result.municipality || result.city || '',
                state: result.state || result.region || '',
                country: result.country || 'México',
                postal_code: result.postcode || '',
                confidence: result.rank?.confidence || 0.8,
                formatted: result.formatted || ''
            };
        }
    } catch (error) {}
    return null;
}

async function getLocationFromMultipleAPIs(lng, lat) {
    const results = [];
    const geoapifyResult = await getLocationFromGeoapify(lng, lat);
    if (geoapifyResult) {
        results.push(geoapifyResult);
    }
    if (results.length === 0) {
        try {
            const nominatimUrl = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1&accept-language=es`;
            const nominatimResponse = await fetch(nominatimUrl);
            const nominatimData = await nominatimResponse.json();
            if (nominatimData && nominatimData.address) {
                results.push({
                    api: 'Nominatim (Backup)',
                    locality: nominatimData.address.village || nominatimData.address.town || nominatimData.address.city || nominatimData.address.locality || '',
                    street: nominatimData.address.road || nominatimData.address.street || '',
                    municipality: nominatimData.address.municipality || nominatimData.address.county || nominatimData.address.city || '',
                    state: nominatimData.address.state || '',
                    country: nominatimData.address.country || 'México',
                    postal_code: nominatimData.address.postcode || '',
                    confidence: nominatimData.importance || 0.5
                });
            }
        } catch (error) {}
    }
    if (results.length === 0) {
        try {
            const mapboxUrl = `https://api.mapbox.com/geocoding/v5/mapbox.places/${lng},${lat}.json?access_token=${mapboxgl.accessToken}`;
            const mapboxResponse = await fetch(mapboxUrl);
            const mapboxData = await mapboxResponse.json();
            if (mapboxData.features && mapboxData.features.length > 0) {
                const context = mapboxData.features[0].context || [];
                const placeFeature = mapboxData.features.find(f => f.place_type?.includes('place')) || 
                                   context.find(c => c.id.includes('place'));
                const addressFeature = mapboxData.features.find(f => f.place_type?.includes('address'));
                results.push({
                    api: 'Mapbox (Último recurso)',
                    locality: addressFeature?.text || placeFeature?.text || mapboxData.features[0].text || '',
                    street: addressFeature?.text || '',
                    municipality: placeFeature?.text || '',
                    state: context.find(c => c.id.includes('region'))?.text || '',
                    country: context.find(c => c.id.includes('country'))?.text || 'México',
                    postal_code: context.find(c => c.id.includes('postcode'))?.text || '',
                    confidence: 0.6
                });
            }
        } catch (error) {}
    }
    return results;
}

function getBestLocationResult(results) {
    if (results.length === 0) return null;
    const combined = {
        locality: '',
        street: '',
        municipality: '',
        state: '',
        country: 'México',
        postal_code: '',
        locality_type: 'address'
    };
    const sortedResults = results.sort((a, b) => {
        const scoreA = (a.municipality ? 2 : 0) + (a.locality ? 1 : 0) + (a.postal_code ? 1 : 0);
        const scoreB = (b.municipality ? 2 : 0) + (b.locality ? 1 : 0) + (b.postal_code ? 1 : 0);
        return scoreB - scoreA;
    });
    const best = sortedResults[0];
    combined.locality = best.locality;
    combined.street = best.street;
    combined.municipality = best.municipality;
    combined.state = best.state;
    combined.country = best.country;
    combined.postal_code = best.postal_code;
    for (const result of sortedResults.slice(1)) {
        if (!combined.municipality && result.municipality) combined.municipality = result.municipality;
        if (!combined.postal_code && result.postal_code) combined.postal_code = result.postal_code;
        if (!combined.state && result.state) combined.state = result.state;
        if (!combined.locality && result.locality) combined.locality = result.locality;
    }
    const municipalityFromComisaria = getMunicipalityFromComisaria(combined.locality);
    if (municipalityFromComisaria) {
        combined.municipality = municipalityFromComisaria;
    }
    return combined;
}

document.addEventListener('DOMContentLoaded', function() {
    mapboxgl.accessToken = 'pk.eyJ1IjoiYW5nZWwwNDE4IiwiYSI6ImNtOG5idHFybzBob3EyaW85NmkxYXZub3EifQ.m1qJwwbbT_wyOqPtDFGb7A';

    is3D = false;
    createMap({
        style: currentStyle2D,
        pitch: lastPitch,
        bearing: lastBearing,
        zoom: lastZoom,
        center: lastCenter
    });

    // =============================
    // Botones Switch 2D / 3D
    // =============================
    document.getElementById('switch-2d-btn').addEventListener('click', function() {
        if (is3D) {
            is3D = false;
            lastCenter = map.getCenter().toArray();
            lastZoom = map.getZoom();
            lastPitch = map.getPitch();
            lastBearing = map.getBearing();
            createMap({
                style: currentStyle2D,
                pitch: lastPitch,
                bearing: lastBearing,
                zoom: lastZoom,
                center: lastCenter
            });
            this.classList.add('active');
            document.getElementById('switch-3d-btn').classList.remove('active');
        }
    });
    document.getElementById('switch-3d-btn').addEventListener('click', function() {
        if (!is3D) {
            is3D = true;
            lastCenter = map.getCenter().toArray();
            lastZoom = map.getZoom();
            lastPitch = 65;
            lastBearing = -30;
            createMap({
                style: currentStyle3D,
                pitch: 65,
                bearing: -30,
                zoom: lastZoom,
                center: lastCenter
            });
            this.classList.add('active');
            document.getElementById('switch-2d-btn').classList.remove('active');
        }
    });
});
</script>
