document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-location');
    const searchBtn = document.getElementById('search-btn');
    const autocompleteList = document.getElementById('autocomplete-list');
    let debounceTimer;

    function showAutocomplete(suggestions) {
        autocompleteList.innerHTML = '';
        suggestions.forEach(place => {
            const item = document.createElement('li');
            item.classList.add('list-group-item');
            item.textContent = place.display_name;
            item.addEventListener('click', function() {
                flyToLocation(map, parseFloat(place.lon), parseFloat(place.lat));
                if (currentMarker) currentMarker.remove();
                currentMarker = new mapboxgl.Marker({ color: '#0d6efd' })
                    .setLngLat([parseFloat(place.lon), parseFloat(place.lat)])
                    .addTo(map);
                autocompleteList.innerHTML = '';
                searchInput.value = place.display_name;

                if (document.getElementById('longitude')) document.getElementById('longitude').value = place.lon;
                if (document.getElementById('latitude')) document.getElementById('latitude').value = place.lat;
                if (document.getElementById('locality')) document.getElementById('locality').value =
                    place.address.city ||
                    place.address.town ||
                    place.address.village ||
                    place.address.locality || '';
                if (document.getElementById('street')) document.getElementById('street').value =
                    place.address.road ||
                    place.address.street ||
                    place.address.neighbourhood ||
                    place.address.suburb ||
                    '';
                if (document.getElementById('postal_code')) document.getElementById('postal_code').value = place.address.postcode || '';
                if (document.getElementById('municipality')) document.getElementById('municipality').value = place.address.municipality || '';
                if (document.getElementById('state')) document.getElementById('state').value = place.address.state || '';
                if (document.getElementById('country')) document.getElementById('country').value = place.address.country || '';
                if (document.getElementById('locality_type')) document.getElementById('locality_type').value = place.type || '';
            });
            autocompleteList.appendChild(item);
        });
    }

    async function searchPlaces(query) {
        if (!query) {
            autocompleteList.innerHTML = '';
            return;
        }
        const url = `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(query + ', Yucatán, México')}&format=json&addressdetails=1&countrycodes=mx&limit=8`;
        try {
            const res = await fetch(url, { headers: { 'Accept-Language': 'es' } });
            const data = await res.json();
            if (data && data.length > 0) {
                showAutocomplete(data);
            } else {
                autocompleteList.innerHTML = '';
            }
        } catch (err) {
            autocompleteList.innerHTML = '';
        }
    }

    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const value = searchInput.value.trim();
        if (value.length < 2) {
            autocompleteList.innerHTML = '';
            return;
        }
        debounceTimer = setTimeout(() => searchPlaces(value), 250);
    });

    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && autocompleteList.firstChild) {
            autocompleteList.firstChild.click();
            e.preventDefault();
        }
    });

    searchBtn.addEventListener('click', function() {
        if (autocompleteList.firstChild) {
            autocompleteList.firstChild.click();
        } else {
            searchPlaces(searchInput.value);
        }
    });

    document.addEventListener('click', function(e) {
        if (!autocompleteList.contains(e.target) && e.target !== searchInput) {
            autocompleteList.innerHTML = '';
        }
    });
});
