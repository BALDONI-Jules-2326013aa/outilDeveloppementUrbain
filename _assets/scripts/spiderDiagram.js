document.addEventListener('DOMContentLoaded', function() {
    // Ensure the function is defined and ready to be called
});

function traiterDonneesGeoJSON(data) {
    let aires = data.features.map(feature => {
        return turf.area(feature);  // Utilisation de Turf.js pour calculer l'aire
    });

    let aireMin = Math.min(...aires);
    let aireMax = Math.max(...aires);
    let aireMoyenne = aires.reduce((a, b) => a + b, 0) / aires.length;

    // Appeler la fonction pour afficher le diagramme avec les aires calculées
    afficherSpiderDiagram(aireMoyenne, aireMin, aireMax);
}

function afficherSpiderDiagram(aireMoyenne, aireMin, aireMax) {
    const ctx = document.getElementById('spiderDiagram').getContext('2d');
    const data = {
        labels: ['Aire Moyenne', 'Aire Min', 'Aire Max'],
        datasets: [{
            label: 'Aires des polygones',
            data: [aireMoyenne, aireMin, aireMax],
            fill: true,
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgb(54, 162, 235)',
            pointBackgroundColor: 'rgb(54, 162, 235)',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: 'rgb(54, 162, 235)'
        }]
    };

    const config = {
        type: 'radar',
        data: data,
        options: {
            scales: {
                r: {
                    beginAtZero: true
                }
            }
        }
    };

    new Chart(ctx, config);
}