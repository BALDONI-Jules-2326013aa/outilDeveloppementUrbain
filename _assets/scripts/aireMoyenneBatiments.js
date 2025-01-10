document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('radarAireMoyenne');
    const chartTypeElement = document.getElementById('chartTypeAireMoyenne');
    const fileNames = JSON.parse(document.getElementById('fileNamesJson').textContent);
    const aireMoyenneData = JSON.parse(document.getElementById('aireMoyenneJson').textContent);


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

    colors = colors.concat(fileNames.slice(10).map(() => ({
        backgroundColor: `#${Math.floor(Math.random() * 16777215).toString(16)}`,
        borderColor: `#${Math.floor(Math.random() * 16777215).toString(16)}`
    })));


    document.getElementById('aireMoyenne').addEventListener('change', function () {
        if (document.getElementById('aireMoyenne').checked) {
            document.getElementById('zoneAireMoyenne').style.display = 'flex';
        } else {
            document.getElementById('zoneAireMoyenne').style.display = 'none';
        }
    });

    let chart = createBarChart();

    function createBarChart() {
        return new Chart(canvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: fileNames,
                datasets: [{
                    label: 'Aire moyenne des batiments',
                    data: aireMoyenneData,
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

    function createLineChart() {
        return new Chart(canvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: fileNames,
                datasets: [{
                    label: 'Aire moyenne des batiments',
                    data: aireMoyenneData,
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

    function createPieChart() {
        return new Chart(canvas.getContext('2d'), {
            type: 'pie',
            data: {
                labels: fileNames,
                datasets: [{
                    data: aireMoyenneData,
                    backgroundColor: colors.map(c => c.backgroundColor)
                }]
            },
            options: {
                responsive: true
            }
        });
    }

    function createRadarChart() {
        return new Chart(canvas.getContext('2d'), {
            type: 'radar',
            data: {
                labels: fileNames,
                datasets: [{
                    label: 'Aire moyenne des batiments',
                    data: aireMoyenneData,
                    backgroundColor: colors.map(c => c.backgroundColor)
                }]
            },
            options: {
                responsive: true,
                scale: {
                    ticks: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    function createDoughnutChart() {
        return new Chart(canvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: fileNames,
                datasets: [{
                    data: aireMoyenneData,
                    backgroundColor: colors.map(c => c.backgroundColor)
                }]
            },
            options: {
                responsive: true
            }
        });
    }

    function createPolarChart() {
        return new Chart(canvas.getContext('2d'), {
            type: 'polarArea',
            data: {
                labels: fileNames,
                datasets: [{
                    data: aireMoyenneData,
                    backgroundColor: colors.map(c => c.backgroundColor)
                }]
            },
            options: {
                responsive: true
            }
        });
    }

    function updateChartType(newType) {
        if (chart) {
            chart.destroy();
        }

        switch (newType) {
            case 'barChartAireMoyenne':
                chart = createBarChart();
                break;
            case 'lineChartAireMoyenne':
                chart = createLineChart();
                break;
            case 'radarChartAireMoyenne':
                chart = createRadarChart();
                break;
            case 'polarChartAireMoyenne':
                chart = createPolarChart();
                break;
            case 'doughnutChartAireMoyenne':
                chart = createDoughnutChart();
                break;
            case 'pieChartAireMoyenne':
                chart = createPieChart();
                break;
            default:
                chart = createBarChart();
        }
    }

    function updateChartColors() {
        fileNames.forEach((_, index) => {
            const colorPicker = document.getElementById(`colorAireMoyenne_${index}`);
            if (colorPicker) {
                colors[index].backgroundColor = colorPicker.value;
                colors[index].borderColor = colorPicker.value;
            }
        });

        chart.data.datasets[0].backgroundColor = colors.map(c => c.backgroundColor);
        chart.update();
    }

    chartTypeElement.addEventListener('change', (event) => {
        const newType = event.target.value;
        updateChartType(newType);
    });

    fileNames.forEach((_, index) => {
        const colorPicker = document.getElementById(`colorAireMoyenne_${index}`);
        if (colorPicker) {
            colorPicker.addEventListener('input', updateChartColors);
        }
    });
});
