document.addEventListener('DOMContentLoaded', function () {
    // Récupère le contexte du canvas pour le graphique
    let ctx = document.getElementById('polarTypeBatiment').getContext('2d');

    // Récupère les données JSON pour les types de bâtiments et les noms de fichiers
    const typeBatimentMap = JSON.parse(document.getElementById('typeBatimentMapJson').textContent);
    const fileNamesPolar = JSON.parse(document.getElementById('fileNamesPolarJson').textContent);

    // Initialise les labels et les données pour le graphique
    let labels = [];
    let data = [];
    for (const [key, value] of Object.entries(typeBatimentMap)) {
        labels.push(`${key} (${value})`);
        data.push(value);
    }

    // Calcule la valeur maximale des données
    const maxValue = Math.max(...data);

    // Initialisation du graphique en polar area
    let polarAreaChart = createPolarTypeBatiments(ctx, labels, data, maxValue);

    // Fonction pour créer un graphique en polar area
    function createPolarTypeBatiments(ctx, labels, data, maxValue) {
        return new Chart(ctx, {
            type: 'polarArea',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Types de bâtiments',
                    data: data,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    r: {
                        beginAtZero: true,
                        min: 0,
                        max: maxValue, // Définit la valeur maximale dynamiquement
                        ticks: {
                            stepSize: 10,
                            font: {
                                size: 18
                            },
                        },
                        pointLabels: {
                            font: {
                                size: 18
                            },
                            color: 'blue'
                        },
                        grid: {
                            circular: true,
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                }
            }
        });
    }

    // Fonction pour ajouter des données GeoJSON au graphique
    function addGeoJson(geojsonData) {
        const buildingTypes = {};
        geojsonData.features.forEach(feature => {
            const type = feature.properties.type;
            if (type) {
                if (!buildingTypes[type]) {
                    buildingTypes[type] = 0;
                }
                buildingTypes[type]++;
            }
        });
        polarAreaChart.data.labels = Object.keys(buildingTypes);
        polarAreaChart.data.datasets[0].data = Object.values(buildingTypes);

        // Met à jour la valeur maximale dynamiquement en fonction des nouvelles données
        const updatedMaxValue = Math.max(...polarAreaChart.data.datasets[0].data);
        polarAreaChart.options.scales.r.max = updatedMaxValue;
        polarAreaChart.update();
    }

    // Ajoute un écouteur d'événement pour le bouton d'ajout de fichier
    document.getElementById('addFileButton').addEventListener('click', function () {
        const fileInput = document.getElementById('fileInput').files[0];

        if (fileInput) {
            const reader = new FileReader();
            reader.onload = function (event) {
                try {
                    const geojsonData = JSON.parse(event.target.result);
                    addGeoJson(geojsonData);
                } catch (e) {
                    console.error('Erreur de parsing GeoJSON:', e);
                }
            };
            reader.readAsText(fileInput);
        } else {
            alert('Veuillez mettre un fichier GeoJSON.');
        }
    });
});