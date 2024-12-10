document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('barBatiments').getContext('2d');
    const fileInputElement = document.getElementById('file2');

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


});
