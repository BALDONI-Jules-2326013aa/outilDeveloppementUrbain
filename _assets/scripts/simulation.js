document.addEventListener('DOMContentLoaded', () => {
    initializeDisplay();
    const map = initializeMap();
    const layers = [];

    geojsonDataArray.forEach((geojsonData, index) => {
        addGeoJSONLayer(map, layers, geojsonData, fileNamesArray[index]);
    });
});

function initializeDisplay() {
    setElementDisplay('downloadFiles', 'flex');
    setElementDisplay('color-selectors', 'flex');
    setElementDisplay('map', 'block');
}

function setElementDisplay(elementId, displayStyle) {
    document.getElementById(elementId).style.display = displayStyle;
}

function initializeMap() {
    const map = L.map('map').setView([0, 0], 2);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        zoomControl: true,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);
    return map;
}

function addGeoJSONLayer(map, layers, geojsonData, fileName) {
    if (isValidGeoJSON(geojsonData)) {
        const color = generateRandomColor();
        const layer = createLayer(map, geojsonData, color);
        layers.push(layer);
        map.fitBounds(layer.getBounds());
        addColorSelector(layer, color, fileName);
    } else {
        console.warn('Fichier GeoJSON vide ou structure invalide');
    }
}

function isValidGeoJSON(geojsonData) {
    return geojsonData && geojsonData.type === 'FeatureCollection';
}

function generateRandomColor() {
    return '#' + Math.floor(Math.random() * 16777215).toString(16);
}

function createLayer(map, geojsonData, color) {
    return L.geoJSON(geojsonData, { style: () => ({ color }) }).addTo(map);
}

function addColorSelector(layer, initialColor, fileName) {
    const colorSelector = createColorSelector(layer, initialColor);

    const label = document.createElement('label');
    label.textContent = 'Couleur pour ' + fileName + ': ';
    label.appendChild(colorSelector);

    document.getElementById('color-selectors').appendChild(label);
}

function createColorSelector(layer, initialColor) {
    const colorSelector = document.createElement('input');
    colorSelector.type = 'color';
    colorSelector.value = initialColor;
    colorSelector.addEventListener('change', () => layer.setStyle({ color: colorSelector.value }));
    return colorSelector;
}
