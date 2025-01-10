document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('recapChartCanva');
    const chartTypeElement = document.getElementById('chartTypeRecap');
    const fileNames = JSON.parse(document.getElementById('fileNamesJson').textContent);
    const aireMoyenneData = JSON.parse(document.getElementById('aireMoyenneJson').textContent);
    const nbBatimentsData = JSON.parse(document.getElementById('nbBatimentsJson').textContent);
    const distanceMoyenneData = JSON.parse(document.getElementById('distanceMoyenneJson').textContent);
    const aireMinData = JSON.parse(document.getElementById('aireMinJson').textContent);
    const aireMaxData = JSON.parse(document.getElementById('aireMaxJson').textContent);
    console.log(aireMinData);
    console.log(aireMaxData);

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


    document.getElementById('btnAfficher').addEventListener('click', function () {
        if (document.getElementById('zoneRecap').style.display === 'flex') {
            document.getElementById('zoneRecap').style.display = 'none';
        } else {
            document.getElementById('zoneRecap').style.display = 'flex';
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
                }, {
                    label: 'Aire minimale',
                    data: aireMinData,
                    backgroundColor: colors.map(c => c.backgroundColor)
                }, {
                    label: 'Aire maximale',
                    data: aireMaxData,
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
                labels: ['Nombre de bâtiments', 'Aire moyenne des bâtiments', 'Distance moyenne entre bâtiments', 'Aire minimale', 'Aire maximale'],
                datasets: fileNames.map((fileName, index) => ({
                    label: fileName,
                    data: [
                        nbBatimentsData[index],
                        aireMoyenneData[index],
                        distanceMoyenneData[index],
                        aireMinData[index],
                        aireMaxData[index]
                    ],
                    backgroundColor: hexToRgba(colors[index].backgroundColor, 0.65)
                }))
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

    function hexToRgba(hex, alpha) {
        hex = hex.replace('#', '');

        const r = parseInt(hex.substring(0, 2), 16);
        const g = parseInt(hex.substring(2, 4), 16);
        const b = parseInt(hex.substring(4, 6), 16);

        return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    }

});