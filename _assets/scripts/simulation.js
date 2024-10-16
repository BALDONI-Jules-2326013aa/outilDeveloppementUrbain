document.addEventListener('DOMContentLoaded', function() {
    initializeDisplay();
    const map = initializeMap();
    const layers = [];
    geojsonDataArray.forEach((geojsonData, index) => {
        addGeoJSONLayer(map, layers, geojsonData, fileNamesArray[index]);
    });
});

function initializeDisplay() {
    document.getElementById('downloadFiles').style.display = 'flex';
    document.getElementById('color-selectors').style.display = 'flex';
    document.getElementById('map').style.display = 'block';
}

function initializeMap() {
    const map = L.map('map').setView([0, 0], 2);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        zoomControl: true,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    return map;
}

function addGeoJSONLayer(map, layers, geojsonData, fileName) {
    if (geojsonData && geojsonData.type === 'FeatureCollection') {
        let color = generateRandomColor();
        const layer = L.geoJSON(geojsonData, { style: () => ({ color }) }).addTo(map);
        layers.push(layer);
        map.fitBounds(layer.getBounds());
        addColorSelector(layer, color, fileName);
    } else {
        console.warn('Fichier GeoJSON vide ou structure invalide');
    }
}

function generateRandomColor() {
    return '#' + Math.floor(Math.random() * 16777215).toString(16);
}

function addColorSelector(layer, color, fileName) {
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
}
