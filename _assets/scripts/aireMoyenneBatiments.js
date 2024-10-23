const aireMoyenneJson = document.getElementById('aireMoyenneJson').textContent;
const fileNamesRadarJson = document.getElementById('fileNamesRadarJson').textContent;

if (aireMoyenneJson && fileNamesRadarJson) {
    const aireMoyenne = JSON.parse(aireMoyenneJson);
    const fileNamesRadar = JSON.parse(fileNamesRadarJson);
    const radarAireMoyenne = document.getElementById('radarAireMoyenne').getContext('2d');


    new Chart(radarAireMoyenne, {
        type: 'radar',
        data: {
            labels: fileNamesRadar,
            datasets: [{
                label: 'Aire moyenne des bâtiments (en m²)',
                data: aireMoyenne,
                backgroundColor: 'rgba(0, 99, 132, 0.2)',
                borderColor: 'rgba(0, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                r: {
                    beginAtZero: true
                }
            }
        }
    });

}

// Fonction pour ajouter un GeoJSON et mettre à jour le graphique
function addGeoJsonToChart(geojsonData, fileName) {
    if (geojsonData.features && geojsonData.features.length > 0) {
        const aireMoyenne = geojsonData.features.reduce((acc, feature) => {
            const aire = turf.area(feature.geometry);
            return acc + aire;
        }, 0) / geojsonData.features.length;

        aireMoyenneData.push(aireMoyenne);
        fileNamesRadar.push(fileName);
        radarAireMoyenne.update(); // Mise à jour du graphique
    } else {
        console.warn(`Le fichier ${fileName} ne contient aucun bâtiment valide.`);
    }
}
S
addFileButton.addEventListener('click', function() {
    const files = fileInputElement.files;

    if (files.length > 0) {
        Array.from(files).forEach(file => {
            const reader = new FileReader();
            reader.onload = (event) => {
                try {
                    const geojsonData = JSON.parse(event.target.result);
                    addGeoJsonToChart(geojsonData, file.name);
                } catch (error) {
                    console.error('Erreur de parsing GeoJSON:', error);
                }
            };
            reader.readAsText(file);
        });
    } else {
        alert('Veuillez sélectionner un fichier GeoJSON.');
    }
});
