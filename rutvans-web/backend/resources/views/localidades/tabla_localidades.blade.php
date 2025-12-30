@if($localidades->count() > 0)
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover" style="border-radius: 8px; overflow: hidden;">
            <thead style="background: linear-gradient(135deg, #ff6600, #ff6600); color: white;">
                <tr>
                    <th style="width: 10%">#</th>
                    <th>Localidad</th>
                    <th>Calle</th>
                    <th>Municipio</th>
                    <th>Estado</th>
                    <th>C.P.</th>
                    <th style="width: 15%" class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($localidades as $index => $localidad)
                    <tr class="localidad-row" data-id="{{ $localidad->id }}" data-lat="{{ $localidad->latitude }}" data-lng="{{ $localidad->longitude }}" style="cursor:pointer;">
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $localidad->locality }}</td>
                        <td>{{ $localidad->street ?: 'N/A' }}</td>
                        <td>{{ $localidad->municipality }}</td>
                        <td>{{ $localidad->state }}</td>
                        <td>{{ $localidad->postal_code ?: 'N/A' }}</td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-warning btn-sm zoom-to-location" 
                                        data-lat="{{ $localidad->latitude }}" 
                                        data-lng="{{ $localidad->longitude }}" 
                                        data-id="{{ $localidad->id }}"
                                        title="Ver en mapa">
                                    <i class="fas fa-search-plus"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" 
                                        onclick="confirmDelete({{ $localidad->id }})"
                                        title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        let lastHighlightedMarker = null;
        let lastHighlightedId = null;
        let tempMarker = null; // Marcador temporal "pum"

        const defaultMarkerStyle = (markerEl) => {
            markerEl.style.filter = '';
        };

        const highlightMarkerStyle = (markerEl) => {
            markerEl.style.filter = 'drop-shadow(0 0 8px orange)';
        };

        function highlightMarker(id) {
            if (!window.markers) return;

            // Quitar resaltado previo
            if (lastHighlightedMarker && lastHighlightedId !== id) {
                defaultMarkerStyle(lastHighlightedMarker.getElement());
                lastHighlightedMarker.togglePopup();
            }

            const marker = window.markers[id];
            if (marker) {
                highlightMarkerStyle(marker.getElement());
                marker.togglePopup();
                lastHighlightedMarker = marker;
                lastHighlightedId = id;
            }
        }

        function unhighlightMarker(id) {
            if (!window.markers) return;
            const marker = window.markers[id];
            if (marker) {
                defaultMarkerStyle(marker.getElement());
                marker.togglePopup();
            }
        }

        // Click en lupa para centrar + resaltar + pum
        document.querySelectorAll('.zoom-to-location').forEach(btn => {
            btn.addEventListener('click', e => {
                e.stopPropagation();
                const lat = parseFloat(btn.getAttribute('data-lat'));
                const lng = parseFloat(btn.getAttribute('data-lng'));
                const id = btn.getAttribute('data-id');

                // Centrar mapa y zoom
                map.flyTo({ center: [lng, lat], zoom: 15 });

                // Resaltar marcador y guardar id resaltado
                highlightMarker(id);

                // Eliminar marcador temporal previo si existe
                if (tempMarker) {
                    tempMarker.remove();
                    tempMarker = null;
                }

                // Crear marcador temporal "pum" pequeño y animado
                const el = document.createElement('div');
                el.style.width = '16px';
                el.style.height = '16px';
                el.style.backgroundColor = 'red';
                el.style.borderRadius = '50%';
                el.style.boxShadow = '0 0 8px 3px rgba(255, 0, 0, 0.7)';
                el.style.animation = 'pulse 1s ease-out';

                tempMarker = new mapboxgl.Marker(el)
                    .setLngLat([lng, lat])
                    .addTo(map);

                setTimeout(() => {
                    if (tempMarker) {
                        tempMarker.remove();
                        tempMarker = null;
                    }
                }, 2000);
            });
        });

        // Hover en fila para quitar el resaltado del marcador resaltado
        document.querySelectorAll('.localidad-row').forEach(row => {
            const id = row.getAttribute('data-id');
            row.addEventListener('mouseenter', () => {
                // Quitar resaltado permanente
                if (lastHighlightedMarker && lastHighlightedId) {
                    unhighlightMarker(lastHighlightedId);
                }
            });
            row.addEventListener('mouseleave', () => {
                // Volver a resaltar el último seleccionado si existe
                if (lastHighlightedId) {
                    highlightMarker(lastHighlightedId);
                }
            });
        });
    });
    </script>

    <style>
        @keyframes pulse {
            0% {
                transform: scale(0.8);
                opacity: 1;
            }
            100% {
                transform: scale(1.6);
                opacity: 0;
            }
        }
    </style>
@endif
