document.addEventListener('DOMContentLoaded', () => {

    // Vérifie si le bouton de téléchargement des fichiers existe et ajoute un écouteur d'événement pour le clic
    if(document.getElementById('downloadFilesButton')) {
        document.getElementById('downloadFilesButton').addEventListener('click', () => {
            const filePath = '/home/jules/Téléchargements/valenicina/donnes_projet/Household_3-2019.geojson';
            const fileName = 'Household_3-2019.geojson';
            console.log('Téléchargement de ' + fileName + ' en cours...');
            const a = document.createElement('a');
            a.href = filePath;
            a.download = fileName;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        });
    }

    // Ajoute des écouteurs d'événements pour afficher les paramètres de la carte et animer le bouton des paramètres
    document.getElementById('mapSettingsButton').addEventListener('click', () => displayPopup('settingsPopup'));
    document.getElementById('mapSettingsButton').addEventListener('click', () => animationMapSettingsButton());

    // Définit l'affichage de certains éléments
    setElementDisplay('mainDisplay', 'flex');
    setElementDisplay('trait', 'flex');
    if(document.getElementById('downloadFiles')) {
        setElementDisplay('downloadFiles', 'flex');
    }

    // Initialise la carte et les couches
    const map = initializeMap();
    const layers = [];

    // Ajoute des couches GeoJSON à la carte pour chaque fichier GeoJSON
    geojsonDataArray.forEach((geojsonData, index) => {
        addGeoJSONLayer(map, layers, geojsonData, fileNamesArray[index]);
    });

    // Ajuste la hauteur de l'élément 'espace' et affiche le menu graphique
    document.getElementById('espace').style.height = '30vh';
    setElementDisplay('menuGraphique', 'flex');

});

// Fonction pour définir l'affichage d'un élément
function setElementDisplay(elementId, displayStyle) {
    document.getElementById(elementId).style.display = displayStyle;
}

// Fonction pour initialiser la carte avec une vue centrée et une couche de tuiles OpenStreetMap
function initializeMap() {
    const map = L.map('map').setView([0, 0], 2);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        zoomControl: true,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);
    return map;
}

// Couleurs prédéfinies pour les couches
const predefinedColors = [
    '#FF5733', '#79fd8c', '#3357FF', '#FF33A8', '#A833FF',
    '#33FFF0', '#FFC733', '#FF8F33', '#8f33ff', '#33FF8F'
];

// Fonction pour vérifier si les données GeoJSON sont valides
function isValidGeoJSON(geojsonData) {
    return geojsonData && geojsonData.type === 'FeatureCollection' && geojsonData.features;
}

// Fonction pour normaliser les données GeoJSON
function normalizeGeoJSON(geojsonData) {
    if (!geojsonData.features && geojsonData.geometry) {
        return {
            type: 'FeatureCollection',
            features: [{
                type: 'Feature',
                geometry: geojsonData.geometry,
                properties: geojsonData.properties || {}
            }]
        };
    }
    return geojsonData;
}

// Fonction pour ajouter une couche GeoJSON à la carte
function addGeoJSONLayer(map, layers, geojsonData, fileName) {
    const normalizedData = normalizeGeoJSON(geojsonData);
    if (isValidGeoJSON(normalizedData)) {
        const color = generateRandomColor(layers.length);
        const layer = createLayer(map, normalizedData, color);
        layers.push(layer);
        map.fitBounds(layer.getBounds());
        createLayerControls(layer, color, fileName, map);
    } else {
        console.warn('Fichier GeoJSON vide ou structure invalide :', fileName);
    }
}

// Fonction pour générer une couleur aléatoire pour une couche
function generateRandomColor(layerIndex) {
    if (layerIndex < predefinedColors.length) {
        return predefinedColors[layerIndex];
    }
    return '#' + Math.floor(Math.random() * 16777215).toString(16);
}

// Fonction pour créer une couche GeoJSON avec une couleur spécifiée
function createLayer(map, geojsonData, color) {
    return L.geoJSON(geojsonData, { style: () => ({ color }) }).addTo(map);
}

// Fonction pour créer des contrôles de couche (sélecteur de couleur et case à cocher de visibilité)
function createLayerControls(layer, color, fileName, map) {
    const colorSelector = createColorSelector(layer, color);
    const visibilityCheckbox = createVisibilityCheckbox(layer, map);

    const label = document.createElement('label');
    label.textContent = '\n' + fileName + ': ';
    label.appendChild(visibilityCheckbox);
    label.appendChild(colorSelector);

    document.getElementById('color-selectors').appendChild(label);
}

// Fonction pour créer un sélecteur de couleur pour une couche
function createColorSelector(layer, initialColor) {
    const colorSelector = document.createElement('input');
    colorSelector.type = 'color';
    colorSelector.value = initialColor;
    colorSelector.addEventListener('change', () => layer.setStyle({ color: colorSelector.value }));
    return colorSelector;
}

// Fonction pour créer une case à cocher de visibilité pour une couche
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

// Fonction pour afficher ou masquer une popup
function displayPopup(elementId) {
    if (document.getElementById(elementId).style.display === 'flex') {
        document.getElementById(elementId).style.display = 'none';
    } else {
        document.getElementById(elementId).style.display = 'flex';
    }
}

// Fonction pour animer le bouton des paramètres de la carte
function animationMapSettingsButton() {
    if(document.getElementById('mapSettingsButton').style.right === '25vw') {
        document.getElementById('mapSettingsButton').style.right = '4vw';
        document.getElementById('mapSettingsButton').style.rotate = '0deg';
    } else {
        document.getElementById('mapSettingsButton').style.right = '25vw';
        document.getElementById('mapSettingsButton').style.rotate = '180deg';
    }
}