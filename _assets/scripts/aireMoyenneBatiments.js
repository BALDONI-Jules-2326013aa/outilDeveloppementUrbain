const aireMoyenneJson = document.getElementById('aireMoyenneJson').textContent;
const fileNamesRadarJson = document.getElementById('fileNamesRadarJson').textContent;

if (aireMoyenneJson && fileNamesRadarJson) {
    const aireMoyenne = JSON.parse(aireMoyenneJson);
    const fileNamesRadar = JSON.parse(fileNamesRadarJson);
    const radarAireMoyenne = document.getElementById('radarAireMoyenne').getContext('2d');

    document.getElementById('radarAireMoyenne').style.display = 'block';

    new Chart(radarAireMoyenne, {
        type: 'radar',
        data: {
            labels: fileNamesRadar,
            datasets: [{
                label: 'Aire moyenne des bâtiments (en m² x 10^6)',
                data: aireMoyenne, // Les valeurs multipliées s'afficheront ici
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
