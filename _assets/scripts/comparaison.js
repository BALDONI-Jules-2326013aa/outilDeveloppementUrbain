document.addEventListener('DOMContentLoaded', () => {

    document.getElementById('mapSettingsButton').addEventListener('click', () => displayPopup('settingsPopup'));
    document.getElementById('mapSettingsButton').addEventListener('click', () => animationMapSettingsButton());

    document.getElementById('graph1').addEventListener('click', () => displayGraphique('barBatiments'));
    document.getElementById('graph2').addEventListener('click', () => displayGraphique('radarAireMoyenne'));

    setElementDisplay('mainDisplay', 'flex');
    setElementDisplay('trait', 'flex');

    const map = initializeMap();
    const layers = [];

    geojsonDataArray.forEach((geojsonData, index) => {
        addGeoJSONLayer(map, layers, geojsonData, fileNamesArray[index]);
    });

});

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

const predefinedColors = [
    '#FF5733', '#33FF57', '#3357FF', '#FF33A8', '#A833FF',
    '#33FFF0', '#FFC733', '#FF8F33', '#8F33FF', '#33FF8F'
];

function addGeoJSONLayer(map, layers, geojsonData, fileName) {
    if (isValidGeoJSON(geojsonData)) {
        const color = generateRandomColor(layers.length);
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

function generateRandomColor(layerIndex) {
    if (layerIndex < predefinedColors.length) {
        return predefinedColors[layerIndex];
    }
    return '#' + Math.floor(Math.random() * 16777215).toString(16);
}

function createLayer(map, geojsonData, color) {
    return L.geoJSON(geojsonData, { style: () => ({ color }) }).addTo(map);
}

function createLayerControls(layer, color, fileName, map) {
    const colorSelector = createColorSelector(layer, color);
    const visibilityCheckbox = createVisibilityCheckbox(layer, map);

    const label = document.createElement('label');
    label.textContent = 'Affichage pour \n' + fileName + ': ';
    label.appendChild(visibilityCheckbox);
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

function displayPopup(elementId) {
    if (document.getElementById(elementId).style.display === 'flex') {
        document.getElementById(elementId).style.display = 'none';
    } else {
        document.getElementById(elementId).style.display = 'flex';
    }
}

function animationMapSettingsButton() {
    if(document.getElementById('mapSettingsButton').style.right === '25vw') {
        document.getElementById('mapSettingsButton').style.right = '4vw';
    } else {
        document.getElementById('mapSettingsButton').style.right = '25vw';
    }
}

function displayGraphique(elementId) {
    const listeGraphiques = ['barBatiments', 'radarAireMoyenne'];
    listeGraphiques.forEach(graphique => {
        if (graphique === elementId) {
            if (document.getElementById(graphique).style.display === 'none') {
                document.getElementById(graphique).style.display = 'block';
            } else {
                document.getElementById(graphique).style.display = 'none';
            }
        } else {
            document.getElementById(graphique).style.display = 'none';
        }
    });
}
