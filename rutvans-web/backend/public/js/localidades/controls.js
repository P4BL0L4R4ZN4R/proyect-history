// controls.js

// Variables para estado
let is3D = false;
let map = null;
let mapLoaded = false;
let currentStyle3D = 'mapbox://styles/mapbox/satellite-streets-v12';
let currentStyle2D = 'mapbox://styles/mapbox/streets-v12';
let lastCenter = [-89.5133, 20.9256];
let lastZoom = 16.5;
let lastPitch = 45;
let lastBearing = -17.6;
let currentMarker = null;
let savedMarkers = [];

// Función para crear el mapa Mapbox GL con opciones personalizadas
function createMap({style, pitch, bearing, zoom, center}) {
    if (map) {
        map.remove();
        map = null;
    }
    map = new mapboxgl.Map({
        container: 'map',
        style,
        center,
        zoom,
        pitch,
        bearing,
        antialias: true
    });
    // Agregar controles clásicos de navegación y pantalla completa
    map.addControl(new mapboxgl.NavigationControl({showCompass: true, showZoom: true}), 'top-right');
    map.addControl(new mapboxgl.FullscreenControl(), 'top-right');

    // Permitir rotación por drag y touch
    map.dragRotate.enable();
    map.touchZoomRotate.enableRotation();

    map.on('load', () => {
        mapLoaded = true;
        map.resize();

        // Si está en 3D, agrega la capa de edificios 3D
        if (is3D) {
            const layers = map.getStyle().layers;
            const labelLayerId = layers.find(layer => layer.type === 'symbol' && layer.layout['text-field'])?.id;
            map.addLayer({
                id: '3d-buildings',
                source: 'composite',
                'source-layer': 'building',
                filter: ['==', 'extrude', 'true'],
                type: 'fill-extrusion',
                minzoom: 15,
                paint: {
                    'fill-extrusion-color': [
                        'interpolate',
                        ['linear'],
                        ['get', 'height'],
                        0, '#d1d5db',
                        20, '#b2bec3',
                        50, '#636e72'
                    ],
                    'fill-extrusion-height': ['get', 'height'],
                    'fill-extrusion-base': ['get', 'min_height'],
                    'fill-extrusion-opacity': 0.85
                }
            }, labelLayerId);
        }

        // Volver a agregar los marcadores guardados (si los hay)
        savedMarkers.forEach(({marker}) => marker.addTo(map));
        if (currentMarker) currentMarker.addTo(map);
    });

    // Evento click para agregar marcador en la posición clickeada
    map.on('click', async (e) => {
        const coords = e.lngLat;

        if (currentMarker) currentMarker.remove();
        currentMarker = new mapboxgl.Marker({color: '#007BFF'}).setLngLat(coords).addTo(map);

        flyToLocation(coords.lng, coords.lat);

        try {
            // Aquí podrías poner llamada para obtener datos de ubicación
            // Ejemplo: obtener datos reverse geocoding y mostrar info en formulario

            // Ejemplo simple para mostrar modal o toast (usa SweetAlert2)
            Swal.fire({
                icon: 'info',
                title: 'Coordenadas',
                html: `Lng: ${coords.lng.toFixed(5)}<br>Lat: ${coords.lat.toFixed(5)}`,
                timer: 2000,
                toast: true,
                position: 'bottom-end',
                showConfirmButton: false
            });
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo obtener la información de ubicación',
                timer: 2000,
                toast: true,
                position: 'bottom-end',
                showConfirmButton: false
            });
        }
    });
}

// Función para hacer "fly" hacia una ubicación en el mapa
function flyToLocation(lng, lat) {
    if (!map) return;
    map.flyTo({
        center: [lng, lat],
        zoom: map.getZoom(),
        bearing: map.getBearing(),
        pitch: map.getPitch(),
        speed: 1.4,
        curve: 1.1,
        easing: t => t
    });
}

// Función para cambiar el estilo y modo del mapa (2D/3D)
function switchMapMode(to3D) {
    if (is3D === to3D) return;

    is3D = to3D;
    if (!map) return;

    // Guardar estado actual
    lastCenter = map.getCenter().toArray();
    lastZoom = map.getZoom();
    lastPitch = to3D ? 65 : 45;
    lastBearing = to3D ? -30 : -17.6;

    createMap({
        style: to3D ? currentStyle3D : currentStyle2D,
        pitch: lastPitch,
        bearing: lastBearing,
        zoom: lastZoom,
        center: lastCenter
    });

    // Actualizar botones de modo activo en UI
    const btn2d = document.getElementById('switch-2d-btn');
    const btn3d = document.getElementById('switch-3d-btn');
    if (to3D) {
        btn3d.classList.add('active');
        btn2d.classList.remove('active');
    } else {
        btn2d.classList.add('active');
        btn3d.classList.remove('active');
    }
}

// Función para inicializar eventos y controles del DOM (botones, input búsqueda)
function initControls() {
    document.getElementById('switch-2d-btn').addEventListener('click', () => switchMapMode(false));
    document.getElementById('switch-3d-btn').addEventListener('click', () => switchMapMode(true));

    // Aquí podrías agregar lógica para el buscador de localidades con autocomplete
    // Similar al código que tienes en tu script principal
}

// Exportar funciones principales si usas módulos (opcional)
// export { createMap, flyToLocation, switchMapMode, initControls };

