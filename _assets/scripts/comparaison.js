document.addEventListener('DOMContentLoaded', () => {
    initializeDisplay();
    const map = initializeMap();
    const layers = [];

    geojsonDataArray.forEach((geojsonData, index) => {
        addGeoJSONLayer(map, layers, geojsonData, fileNamesArray[index]);
    });

    document.getElementById('addFileButton').addEventListener('click', () => {
        handleFileUpload(map, layers);
    });
});

function initializeDisplay() {
    setElementDisplay('mainDisplay', 'flex');
    setElementDisplay('leftPart', 'flex');
    setElementDisplay('addFileContainer', 'flex');
    setElementDisplay('trait', 'flex');
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
        createLayerControls(layer, color, fileName, map);
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

function createLayerControls(layer, color, fileName, map) {
    const colorSelector = createColorSelector(layer, color);
    const visibilityCheckbox = createVisibilityCheckbox(layer, map);

    const label = document.createElement('label');
    label.textContent = 'Affichage pour ' + fileName + ': ';
    label.appendChild(colorSelector);
    label.appendChild(visibilityCheckbox);

    document.getElementById('color-selectors').appendChild(label);
}

function createColorSelector(layer, initialColor) {
    const colorSelector = document.createElement('input');
    colorSelector.type = 'color';
    colorSelector.value = initialColor;
    colorSelector.addEventListener('change', () => layer.setStyle({ color: colorSelector.value }));
    return colorSelector;
}

function createVisibilityCheckbox(layer, map) {
    const visibilityCheckbox = document.createElement('input');
    visibilityCheckbox.type = 'checkbox';
    visibilityCheckbox.checked = true;
    visibilityCheckbox.addEventListener('change', () => {
        if (visibilityCheckbox.checked) {
            map.addLayer(layer);
        } else {
            map.removeLayer(layer);
        }
    });
    return visibilityCheckbox;
}

function handleFileUpload(map, layers) {
    const fileInput = document.getElementById('file2').files[0];

    if (fileInput) {
        const reader = new FileReader();
        reader.onload = (event) => {
            try {
                const geojsonData = JSON.parse(event.target.result);
                addGeoJSONLayer(map, layers, geojsonData, fileInput.name);
                document.getElementById('file2').value = '';
            } catch (error) {
                console.error('Erreur de parsing GeoJSON:', error);
            }
        };
        reader.readAsText(fileInput);
    } else {
        alert('Veuillez sélectionner un fichier GeoJSON.');
    }
}
