document.addEventListener('DOMContentLoaded', function() {
    let ctx = document.getElementById('barBatiments').getContext('2d');

    let labels = JSON.parse(document.getElementById('fileNamesJson').textContent);
    let nbBatimentsData = JSON.parse(document.getElementById('nbBatimentsJson').textContent);

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

    function addGeoJson(geojsonData, fileName) {
        const nbBatiments = geojsonData.features ? geojsonData.features.length : 0;

        if (nbBatiments > 0) {
            labels.push(fileName);
            nbBatimentsData.push(nbBatiments);

            barBatiments.update();
        }
    }

    document.getElementById('addFileButton').addEventListener('click', function() {
        const files = document.getElementById('file2').files;

        if (files.length > 0) {
            Array.from(files).forEach(fileInput => {
                const reader = new FileReader();
                reader.onload = (event) => {
                    try {
                        const geojsonData = JSON.parse(event.target.result);
                        addGeoJson(geojsonData, fileInput.name);
                    } catch (error) {
                        console.error('Erreur de parsing GeoJSON:', error);
                    }
                };
                reader.readAsText(fileInput);
            });
            document.getElementById('file2').value = '';  // Réinitialisation après la boucle
        }
    });
});
