document.addEventListener('DOMContentLoaded', function() {
    const map = L.map('map').setView([0, 0], 2);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        zoomControl: true,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    const layers = [];

    geojsonDataArray.forEach(function(geojsonData, index) {
        if (geojsonData && geojsonData.type === 'FeatureCollection') {
            let color = '#' + Math.floor(Math.random() * 16777215).toString(16);
            const layer = L.geoJSON(geojsonData, {
                style: function() {
                    return { color: color };
                }
            }).addTo(map);

            layers.push(layer);
            map.fitBounds(layer.getBounds());

            const colorSelector = document.createElement('input');
            colorSelector.type = 'color';
            colorSelector.value = color;
            colorSelector.addEventListener('change', function() {
                layer.setStyle({ color: colorSelector.value });
            });

            const label = document.createElement('label');
            label.textContent = 'Couleur pour ' + fileNamesArray[index] + ': '; // Utilise le nom du fichier
            label.appendChild(colorSelector);
            document.getElementById('color-selectors').appendChild(label);
        } else {
            console.warn('Fichier GeoJSON vide ou structure invalide');
        }
    });
});
