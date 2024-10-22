document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('radarAireBatiments').getContext('2d');
    const fileInputElement = document.getElementById('file2');
    const addFileButton = document.getElementById('addFileButton');

    let labels = [];  // Les noms des fichiers
    let aireBatimentsData = [];  // Stocker la surface des bâtiments de chaque fichier

    // Initialisation du graphique radar
    let radarChart = createRadarChart(ctx, labels, aireBatimentsData);

    // Fonction pour calculer l'aire d'un bâtiment (en supposant que la géométrie soit un polygone)
    function calculateBuildingArea(geojsonFeature) {
        // Implémentation simplifiée : on peut utiliser une librairie comme turf.js pour calculer précisément les surfaces
        if (geojsonFeature.geometry && geojsonFeature.geometry.type === 'Polygon') {
            let coordinates = geojsonFeature.geometry.coordinates[0];
            let area = 0;

            for (let i = 0; i < coordinates.length - 1; i++) {
                let [x1, y1] = coordinates[i];
                let [x2, y2] = coordinates[i + 1];
                area += (x1 * y2 - x2 * y1);  // Calcul de l'aire via la formule des polygones
            }

            return Math.abs(area / 2);  // Aire absolue
        }
        return 0;
    }

    // Fonction pour calculer la surface totale des bâtiments dans un fichier GeoJSON
    function calculateTotalBuildingArea(geojsonData) {
        return geojsonData.features
            .filter(feature => feature.geometry && feature.geometry.type === 'Polygon')
            .reduce((totalArea, feature) => totalArea + calculateBuildingArea(feature), 0);
    }

    // Fonction pour créer le graphique radar
    function createRadarChart(ctx, labels, data) {
        return new Chart(ctx, {
            type: 'radar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Surface totale des bâtiments (en unités)',
                    data: data,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    r: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Fonction pour ajouter un GeoJSON et mettre à jour le graphique radar
    function addGeoJsonToChart(geojsonData, fileName) {
        const totalAireBatiments = calculateTotalBuildingArea(geojsonData);

        if (totalAireBatiments > 0) {
            labels.push(fileName);
            aireBatimentsData.push(totalAireBatiments);

            radarChart.update();  // Mise à jour du graphique radar
        } else {
            console.warn(`Le fichier ${fileName} ne contient aucun bâtiment valide.`);
        }
    }

    // Gestion de l'ajout de fichier via le bouton
    addFileButton.addEventListener('click', function() {
        const files = fileInputElement.files;

        if (files.length > 0) {
            Array.from(files).forEach(file => {
                const reader = new FileReader();
                reader.onload = event => {
                    try {
                        const geojsonData = JSON.parse(event.target.result);
                        addGeoJsonToChart(geojsonData, file.name);
                    } catch (error) {
                        console.error('Erreur de parsing GeoJSON:', error);
                    }
                };
                reader.readAsText(file);
            });
            fileInputElement.value = '';  // Réinitialisation de l'input
        }
    });
});
