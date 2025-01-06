document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('barBatiments');
    const chartTypeElement = document.getElementById('chartType');
    const fileNames = JSON.parse(document.getElementById('fileNamesJson').textContent);
    const nbBatimentsData = JSON.parse(document.getElementById('nbBatimentsJson').textContent);

    let colors = fileNames.map(() => ({
        backgroundColor: `#${Math.floor(Math.random() * 16777215).toString(16)}`,
        borderColor: `#${Math.floor(Math.random() * 16777215).toString(16)}`
    }));

    let currentChartType = 'bar';
    let chart = createBarChart();

    function createBarChart() {
        return new Chart(canvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: fileNames,
                datasets: [{
                    label: 'Nombre de bâtiments',
                    data: nbBatimentsData,
                    backgroundColor: colors.map(c => c.backgroundColor),
                    borderColor: colors.map(c => c.borderColor),
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

    function createLineChart() {
        return new Chart(canvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: fileNames,
                datasets: [{
                    label: 'Nombre de bâtiments',
                    data: nbBatimentsData,
                    backgroundColor: colors.map(c => c.backgroundColor),
                    borderColor: colors.map(c => c.borderColor),
                    borderWidth: 2,
                    fill: false
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
                    data: nbBatimentsData,
                    backgroundColor: colors.map(c => c.backgroundColor),
                    borderColor: colors.map(c => c.borderColor)
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
                    label: 'Nombre de bâtiments',
                    data: nbBatimentsData,
                    backgroundColor: colors.map(c => c.backgroundColor),
                    borderColor: colors.map(c => c.borderColor),
                    borderWidth: 1
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
                    data: nbBatimentsData,
                    backgroundColor: colors.map(c => c.backgroundColor),
                    borderColor: colors.map(c => c.borderColor)
                }]
            },
            options: {
                responsive: true
            }
        });
    }

    function createPolarAreaChart() {
        return new Chart(canvas.getContext('2d'), {
            type: 'polarArea',
            data: {
                labels: fileNames,
                datasets: [{
                    data: nbBatimentsData,
                    backgroundColor: colors.map(c => c.backgroundColor),
                    borderColor: colors.map(c => c.borderColor)
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
            case 'barChartNbBatiments':
                chart = createBarChart();
                break;
            case 'lineChartNbBatiments':
                chart = createLineChart();
                break;
            case 'pieChartNbBatiments':
                chart = createPieChart();
                break;
            case 'radarChartNbBatiments':
                chart = createRadarChart();
                break;
            case 'doughnutChartNbBatiments':
                chart = createDoughnutChart();
                break;
            case 'polarChartNbBatiments':
                chart = createPolarAreaChart();
                break;
            default:
                chart = createBarChart();
        }
    }

    function updateChartColors() {
        fileNames.forEach((_, index) => {
            const colorPicker = document.getElementById(`color_${index}`);
            if (colorPicker) {
                colors[index].backgroundColor = colorPicker.value;
                colors[index].borderColor = colorPicker.value;
            }
        });

        chart.data.datasets[0].backgroundColor = colors.map(c => c.backgroundColor);
        chart.data.datasets[0].borderColor = colors.map(c => c.borderColor);
        chart.update();
    }

    chartTypeElement.addEventListener('change', (event) => {
        const newType = event.target.value;
        updateChartType(newType);
    });

    fileNames.forEach((_, index) => {
        const colorPicker = document.getElementById(`color_${index}`);
        if (colorPicker) {
            colorPicker.addEventListener('input', updateChartColors);
        }
    });
});
