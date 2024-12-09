document.addEventListener('DOMContentLoaded', function () {
    let ctx = document.getElementById('polarAreaChart').getContext('2d');

    let labels = [];
    let data = [];

    let polarAreaChart = new Chart(ctx, {
        type: 'polarArea',
        data: {
            labels: labels,
            datasets: [{
                label: 'Types de batiments',
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