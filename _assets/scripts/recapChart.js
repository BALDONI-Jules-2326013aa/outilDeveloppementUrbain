document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('graphiqueRecap');
    const chartTypeElement = document.getElementById('chartTypeRecap');
    const fileNames = JSON.parse(document.getElementById('fileNamesJson').textContent);
    const aireMoyenneData = JSON.parse(document.getElementById('aireMoyenneJson').textContent);
    const nbBatimentsData = JSON.parse(document.getElementById('nbBatimentsJson').textContent);
    const distanceMoyenneData = JSON.parse(document.getElementById('distanceMoyenneJson').textContent);

    let colors = fileNames.map(() => ({
        backgroundColor: `#${Math.floor(Math.random() * 16777215).toString(16)}`,
        borderColor: `#${Math.floor(Math.random() * 16777215).toString(16)}`
    }));

    // Gestion de l'affichage du graphique
    document.getElementById('btnAfficher').addEventListener('click', function () {
        if (document.getElementById('recap').style.display === 'flex') {
            document.getElementById('recap').style.display = 'none';
        } else {
            document.getElementById('recap').style.display = 'flex';
        }
    });

    let chart = createBarChart();

    function createBarChart() {
        return new Chart(canvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: fileNames,
                datasets: [{
                    label: 'Nombre de bâtiments',
                    data: nbBatimentsData,
                    backgroundColor: colors.map(c => c.backgroundColor)
                }, {
                    label: 'Aire moyenne des bâtiments',
                    data: aireMoyenneData,
                    backgroundColor: colors.map(c => c.backgroundColor)
                }, {
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

    function createRadarChart() {
        return new Chart(canvas.getContext('2d'), {
            type: 'radar',
            data: {
                labels: fileNames,
                datasets: [{
                    label: 'Nombre de bâtiments',
                    data: nbBatimentsData,
                    backgroundColor: colors.map(c => c.backgroundColor)
                }, {
                    label: 'Aire moyenne des bâtiments',
                    data: aireMoyenneData,
                    backgroundColor: colors.map(c => c.backgroundColor)
                }, {
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

    function updateChartType(newType) {
        if (chart) {
            chart.destroy();
        }

        switch (newType) {
            case 'barChartRecap':
                chart = createBarChart();
                break;
            case 'radarChartRecap':
                chart = createRadarChart();
                break;
            default:
                chart = createBarChart();
                break;
        }
    }

    chartTypeElement.addEventListener('change', function () {
        updateChartType(chartTypeElement.value);
    });

    function updateChartColors() {
        fileNames.forEach((_, index) => {
            const colorPicker = document.getElementById(`colorRecap_${index}`);
            if (colorPicker) {
                colors[index].backgroundColor = colorPicker.value;
                colors[index].borderColor = colorPicker.value;
            }
        });
    }

    fileNames.forEach((_, index) => {
        const colorPicker = document.getElementById(`colorRecap_${index}`);
        if (colorPicker) {
            colorPicker.addEventListener('change', function () {
                colors[index].backgroundColor = colorPicker.value;
                colors[index].borderColor = colorPicker.value;
                chart.data.datasets.forEach((dataset) => {
                    dataset.backgroundColor[index] = colorPicker.value;
                    dataset.borderColor[index] = colorPicker.value;
                });
                chart.update();
            });
        }
    });

});