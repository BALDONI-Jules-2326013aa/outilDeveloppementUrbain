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
    // on définit espg3460 car il est utilisé dans les données qu'on a pour tester
    const epsg3460 = new L.Proj.CRS('EPSG:3460',
        '+proj=tmerc +lat_0=0 +lon_0=173 +k=0.9996 +x_0=1600000 +y_0=10000000 +datum=WGS84 +units=m +no_defs',
        {
            resolutions: [
                8192, 4096, 2048, 1024, 512, 256, 128, 64, 32, 16, 8, 4, 2, 1
            ],
            origin: [0, 0]
        }
    );

    // Liste étendue des CRS valides
    const validCrs = {
        '4326': L.CRS.EPSG4326, // WGS 84 (latitude/longitude)
        '3857': L.CRS.EPSG3857, // Pseudo-Mercator
        '3395': L.CRS.EPSG3395, // Mercator
        '2154': L.CRS.EPSG2154, // RGF93 / Lambert-93 (France)
        '27700': L.CRS.EPSG27700, // OSGB36 / British National Grid
        '3035': L.CRS.EPSG3035, // ETRS89 / Lambert Azimuthal Equal Area
        '3413': L.CRS.EPSG3413, // NSIDC Sea Ice Polar Stereographic North
        '32633': L.CRS.EPSG32633, // WGS 84 / UTM zone 33N
        '32733': L.CRS.EPSG32733, // WGS 84 / UTM zone 33S
        '3460': epsg3460, // NZGD2000 / NZTM2000
    };

    // On récupère le contenu de la balise #crs
    let crs = document.getElementById('crs').textContent;

    // Détermine le CRS à utiliser
    let crsOption;
    if (crs !== 'default') {
        const crsKey = crs.slice(-4); // Garde les 4 derniers caractères (ou 5 si nécessaire)
        crsOption = validCrs[crsKey] || null;

        if (!crsOption) {
            console.warn(`CRS non reconnu : ${crs}. Utilisation du CRS par défaut EPSG3857.`);
            crsOption = L.CRS.EPSG3857; // Par défaut, EPSG3857
        }
    } else {
        crsOption = L.CRS.EPSG3857; // Par défaut, EPSG3857
    }

    // Initialisation de la carte
    const map = L.map('map', {
        crs: crsOption,
        center: [0, 0],
        zoom: 2
    });

    // Ajout du TileLayer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: 'Données cartographiques © <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
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