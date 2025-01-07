document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('barDistanceMoyenne');
    const chartTypeElement = document.getElementById('chartTypeDistanceMoyenne');
    const fileNames = JSON.parse(document.getElementById('fileNamesJson').textContent);
    const distanceMoyenneData = JSON.parse(document.getElementById('distanceMoyenneJson').textContent);

    // Générer des couleurs initiales aléatoires
    let colors = fileNames.map(() => ({
        backgroundColor: `#${Math.floor(Math.random() * 16777215).toString(16)}`,
        borderColor: `#${Math.floor(Math.random() * 16777215).toString(16)}`
    }));

    // Gestion de l'affichage du graphique
    document.getElementById('distanceMoyenne').addEventListener('change', function () {
        if (document.getElementById('distanceMoyenne').checked) {
            document.getElementById('zoneDistanceMoyenne').style.display = 'flex';
        } else {
            document.getElementById('zoneDistanceMoyenne').style.display = 'none';
        }
    });

    let chart = createBarChart();

    // 📊 Fonction pour créer un graphique en barres
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

    // 📈 Fonction pour créer un graphique en lignes
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

    // 🥧 Fonction pour créer un graphique en camembert
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

    // 📊 Fonction pour créer un graphique radar
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

    // 🍩 Fonction pour créer un graphique en donut
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

    // 🧭 Fonction pour créer un graphique polaire
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

    // 🔄 Fonction pour changer le type de graphique
    function updateChartType(newType) {
        if (chart) {
            chart.destroy();
        }

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

    // 🎨 Fonction pour mettre à jour les couleurs
    function updateChartColors() {
        fileNames.forEach((_, index) => {
            const colorPicker = document.getElementById(`colorDistanceMoyenne_${index}`);
            if (colorPicker) {
                colors[index].backgroundColor = colorPicker.value;
                colors[index].borderColor = colorPicker.value;
            }
        });

        chart.data.datasets[0].backgroundColor = colors.map(c => c.backgroundColor);
        chart.data.datasets[0].borderColor = colors.map(c => c.borderColor);
        chart.update();
    }

    // 🎯 Écouteur pour le changement de type de graphique
    chartTypeElement.addEventListener('change', (event) => {
        const newType = event.target.value;
        updateChartType(newType);
    });

    // 🎯 Écouteurs pour les changements de couleurs
    fileNames.forEach((_, index) => {
        const colorPicker = document.getElementById(`colorDistanceMoyenne_${index}`);
        if (colorPicker) {
            colorPicker.addEventListener('input', updateChartColors);
        }
    });
});
