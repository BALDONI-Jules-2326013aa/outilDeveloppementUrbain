document.addEventListener('DOMContentLoaded', function () {
    // Récupère les éléments du DOM nécessaires
    const canvas = document.getElementById('barDistanceMoyenne');
    const chartTypeElement = document.getElementById('chartTypeDistanceMoyenne');
    const fileNames = JSON.parse(document.getElementById('fileNamesJson').textContent);
    const distanceMoyenneData = JSON.parse(document.getElementById('distanceMoyenneJson').textContent);

    // Définit les couleurs par défaut pour les graphiques
    let colors = [
        { backgroundColor: '#FF5733', borderColor: '#FF5733' },  // Rouge vif
        { backgroundColor: '#79fd8c', borderColor: '#79fd8c' },  // Vert clair
        { backgroundColor: '#3357FF', borderColor: '#3357FF' },  // Bleu vif
        { backgroundColor: '#FF33A8', borderColor: '#FF33A8' },  // Rose vif
        { backgroundColor: '#A833FF', borderColor: '#A833FF' },  // Violet
        { backgroundColor: '#33FFF0', borderColor: '#33FFF0' },  // Bleu cyan
        { backgroundColor: '#FFC733', borderColor: '#FFC733' },  // Jaune vif
        { backgroundColor: '#FF8F33', borderColor: '#FF8F33' },  // Orange vif
        { backgroundColor: '#8f33ff', borderColor: '#8f33ff' },  // Violet foncé
        { backgroundColor: '#33FF8F', borderColor: '#33FF8F' }   // Vert vif
    ];

    // Ajoute des couleurs aléatoires si le nombre de fichiers dépasse 10
    colors = colors.concat(fileNames.slice(10).map(() => ({
        backgroundColor: `#${Math.floor(Math.random() * 16777215).toString(16)}`,
        borderColor: `#${Math.floor(Math.random() * 16777215).toString(16)}`
    })));

    // Affiche ou cache la zone de la distance moyenne en fonction de la case à cocher
    document.getElementById('distanceMoyenne').addEventListener('change', function () {
        if (document.getElementById('distanceMoyenne').checked) {
            document.getElementById('zoneDistanceMoyenne').style.display = 'flex';
        } else {
            document.getElementById('zoneDistanceMoyenne').style.display = 'none';
        }
    });

    // Crée le graphique initial (bar chart)
    let chart = createBarChart();

    // Fonction pour créer un bar chart
    function createBarChart() {
        return new Chart(canvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: fileNames,
                datasets: [{
                    label: 'Distance moyenne entre bâtiments',
                    data: distanceMoyenneData,
                    backgroundColor: colors.map(c => c.backgroundColor)
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Fonction pour créer un line chart
    function createLineChart() {
        return new Chart(canvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: fileNames,
                datasets: [{
                    label: 'Distance moyenne entre bâtiments',
                    data: distanceMoyenneData,
                    backgroundColor: colors.map(c => c.backgroundColor),
                    fill: true
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

    // Fonction pour créer un pie chart
    function createPieChart() {
        return new Chart(canvas.getContext('2d'), {
            type: 'pie',
            data: {
                labels: fileNames,
                datasets: [{
                    data: distanceMoyenneData,
                    backgroundColor: colors.map(c => c.backgroundColor)
                }]
            },
            options: {
                responsive: true
            }
        });
    }

    // Fonction pour créer un radar chart
    function createRadarChart() {
        return new Chart(canvas.getContext('2d'), {
            type: 'radar',
            data: {
                labels: fileNames,
                datasets: [{
                    label: 'Distance moyenne entre bâtiments',
                    data: distanceMoyenneData,
                    backgroundColor: colors.map(c => c.backgroundColor)
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

    // Fonction pour créer un doughnut chart
    function createDoughnutChart() {
        return new Chart(canvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: fileNames,
                datasets: [{
                    data: distanceMoyenneData,
                    backgroundColor: colors.map(c => c.backgroundColor)
                }]
            },
            options: {
                responsive: true
            }
        });
    }

    // Fonction pour créer un polar area chart
    function createPolarAreaChart() {
        return new Chart(canvas.getContext('2d'), {
            type: 'polarArea',
            data: {
                labels: fileNames,
                datasets: [{
                    data: distanceMoyenneData,
                    backgroundColor: colors.map(c => c.backgroundColor)
                }]
            },
            options: {
                responsive: true
            }
        });
    }

    // Met à jour le type de graphique en fonction de la sélection de l'utilisateur
    function updateChartType(newType) {
        if (chart) {
            chart.destroy(); // Détruit le graphique existant
        }

        // Crée un nouveau graphique en fonction du type sélectionné
        switch (newType) {
            case 'barChartDistanceMoyenne':
                chart = createBarChart();
                break;
            case 'lineChartDistanceMoyenne':
                chart = createLineChart();
                break;
            case 'pieChartDistanceMoyenne':
                chart = createPieChart();
                break;
            case 'radarChartDistanceMoyenne':
                chart = createRadarChart();
                break;
            case 'doughnutChartDistanceMoyenne':
                chart = createDoughnutChart();
                break;
            case 'polarChartDistanceMoyenne':
                chart = createPolarAreaChart();
                break;
            default:
                chart = createBarChart();
        }
    }

    // Ajoute un écouteur d'événement pour changer le type de graphique
    chartTypeElement.addEventListener('change', (event) => {
        const newType = event.target.value;
        updateChartType(newType); // Met à jour le type de graphique en fonction de la sélection de l'utilisateur
    });
});