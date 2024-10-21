document.addEventListener('DOMContentLoaded', function() {
    let ctx = document.getElementById('barBatiments').getContext('2d');

    // Initialisation des labels et des datasets
    let labels = JSON.parse(document.getElementById('fileNamesJson').textContent);
    let nbBatimentsData = JSON.parse(document.getElementById('nbBatimentsJson').textContent);

    // Création initiale du graphique
    let barBatiments = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Nombre de bâtiments',
                data: nbBatimentsData,
                backgroundColor: '#6b5eba',
                borderColor: '#557002',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Fonction pour ajouter des données GeoJSON et mettre à jour le graphique
    function addGeoJson(geojsonData, fileName) {
        // Simuler l'extraction du nombre de bâtiments (par exemple, nombre de "features")
        const nbBatiments = geojsonData.features ? geojsonData.features.length : 0;

        // Ajouter le nouveau fichier et son nombre de bâtiments aux labels et datasets
        barBatiments.data.labels.push(fileName);
        barBatiments.data.datasets[0].data.push(nbBatiments);

        // Mettre à jour le graphique avec les nouvelles données
        barBatiments.update();
    }

    // Gestionnaire pour l'ajout d'un fichier GeoJSON
    document.getElementById('addFileButton').addEventListener('click', function() {
        const newFileInput = document.getElementById('file2').files[0];

        if (newFileInput) {
            const reader = new FileReader();
            reader.onload = function(event) {
                try {
                    const geojsonData = JSON.parse(event.target.result);
                    // Ajouter les données du nouveau fichier au graphique
                    addGeoJson(geojsonData, newFileInput.name);

                    // Réinitialiser le champ de fichier après l'ajout
                    document.getElementById('file2').value = '';
                } catch (e) {
                    console.error('Erreur de parsing GeoJSON:', e);
                }
            };
            reader.readAsText(newFileInput);
        } else {
            alert('Veuillez sélectionner un fichier GeoJSON.');
        }
    });
});
