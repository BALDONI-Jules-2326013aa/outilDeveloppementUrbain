document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('downloadFiles').style.display = 'flex';
    document.getElementById('color-selectors').style.display = 'flex';
    document.getElementById('map').style.display = 'block';

    const map = L.map('map').setView([0, 0], 2);

    console.log(geojsonDataArray);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        zoomControl: true,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    const layers = [];

    function addGeoJSONLayer(geojsonData, fileName) {
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
            label.textContent = 'Couleur pour ' + fileName + ': ';
            label.appendChild(colorSelector);
            document.getElementById('color-selectors').appendChild(label);
        } else {
            console.warn('Fichier GeoJSON vide ou structure invalide');
        }
    }

    geojsonDataArray.forEach((geojsonData, index) => {
        addGeoJSONLayer(geojsonData, fileNamesArray[index]);
    });


});
