document.addEventListener('DOMContentLoaded', function () {
    let ctx = document.getElementById('polarTypeBatiment').getContext('2d');


    const typeBatimentMap = JSON.parse(document.getElementById('typeBatimentMapJson').textContent);
    const fileNamesPolar = JSON.parse(document.getElementById('fileNamesPolarJson').textContent);
    let labels = [];
    let data = [];
    for (const [key, value] of Object.entries(typeBatimentMap)) {
        labels.push(key);
        data.push(value);
    }

    /*
    let polarAreaChart = new Chart(ctx, {
        type: 'polarArea',
        data: {
            labels: labels,
            datasets: [{
                label: fileNamesPolarJson, // 'Types de batiments',
                data:data,
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
                    beginAtZero: true
                }
            }
        }
    });
    */

    // Initialisation du graphique
    let barTypeBatiments = createBarChartTypeBatiments(ctx, labels, data);

    // Fonction pour créer un graphique à barres
    function createBarChartTypeBatiments(ctx, labels, data) {
        const COLORS = {
            backgroundColor: '#6b5eba',
            borderColor: '#557002'
        };

        return new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Types de bâtiments',
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
        polarAreaChart.update();
    }

    document.getElementById('addFileButton').addEventListener('click', function() {
        const fileInput = document.getElementById('fileInput').files[0];

        if (fileInput) {
            const reader = new FileReader();
            reader.onload = function(event) {
               try {
                   const geojsonData = JSON.parse(event.target.result);
                   addGeoJson(geojsonData);
               } catch (e) {
                    console.error('Erreur de parsing GeoJSON:', e);
               }
            };
            reader.readAsText(fileInput);
        } else {
            alert('Veuillez mettre un fichier GeoJson.');
        }
    });
});