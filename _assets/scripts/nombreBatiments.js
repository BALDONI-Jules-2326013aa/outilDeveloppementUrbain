document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('barBatiments').getContext('2d');
    const fileInputElement = document.getElementById('file2');
    const addFileButton = document.getElementById('addFileButton');

    const COLORS = {
        backgroundColor: '#6b5eba',
        borderColor: '#557002'
    };

    let labels = JSON.parse(document.getElementById('fileNamesJson').textContent);
    let nbBatimentsData = JSON.parse(document.getElementById('nbBatimentsJson').textContent);

    // Initialisation du graphique
    let barBatiments = createBarChart(ctx, labels, nbBatimentsData);

    // Fonction pour créer un graphique à barres
    function createBarChart(ctx, labels, data) {
        return new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Nombre de bâtiments',
                    data: data,
                    backgroundColor: COLORS.backgroundColor,
                    borderColor: COLORS.borderColor,
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
    }

    // Fonction pour ajouter un GeoJSON et mettre à jour le graphique
    function addGeoJsonToChart(geojsonData, fileName) {
        if (geojsonData.features && geojsonData.features.length > 0) {
            const nbBatiments = geojsonData.features.length;
            labels.push(fileName);
            nbBatimentsData.push(nbBatiments);
            barBatiments.update(); // Mise à jour du graphique
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
        }
    });
});
