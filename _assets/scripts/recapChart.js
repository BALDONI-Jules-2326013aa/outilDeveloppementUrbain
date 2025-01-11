document.addEventListener('DOMContentLoaded', function () {
    // Récupère les éléments du DOM nécessaires
    const canvas = document.getElementById('recapChartCanva');
    const chartTypeElement = document.getElementById('chartTypeRecap');
    const fileNames = JSON.parse(document.getElementById('fileNamesJson').textContent);
    const aireMoyenneData = JSON.parse(document.getElementById('aireMoyenneJson').textContent);
    const nbBatimentsData = JSON.parse(document.getElementById('nbBatimentsJson').textContent);
    const distanceMoyenneData = JSON.parse(document.getElementById('distanceMoyenneJson').textContent);
    const aireMinData = JSON.parse(document.getElementById('aireMinJson').textContent);
    const aireMaxData = JSON.parse(document.getElementById('aireMaxJson').textContent);
    const perimetreMoyenData = JSON.parse(document.getElementById('perimetreMoyenJson').textContent);
    const perimetreMinData = JSON.parse(document.getElementById('perimetreMinJson').textContent);
    const perimetreMaxData = JSON.parse(document.getElementById('perimetreMaxJson').textContent);

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

    // Affiche ou cache la zone de récapitulation en fonction de la case à cocher
    document.getElementById('btnAfficher').addEventListener('click', function () {
        if (document.getElementById('zoneRecap').style.display === 'flex') {
            document.getElementById('zoneRecap').style.display = 'none';
        } else {
            document.getElementById('zoneRecap').style.display = 'flex';
        }
    });

    // Crée le graphique initial (radar chart normalisé)
    let chart = createRadarChartNormalized();

    // Fonction pour créer un bar chart
    function createRadarChartNormalized() {
        return new Chart(canvas.getContext('2d'), {
            type: 'radar',
            data: {
                labels: ['Nombre de bâtiments', 'Aire moyenne des bâtiments', 'Distance moyenne entre bâtiments', 'Aire minimale', 'Aire maximale', 'Périmètre moyen', 'Périmètre minimal', 'Périmètre maximal'],
                datasets: fileNames.map((fileName, index) => ({
                    label: fileName,
                    data: [
                        normalizeValue(nbBatimentsData[index], nbBatimentsData),
                        normalizeValue(aireMoyenneData[index], aireMoyenneData),
                        normalizeValue(distanceMoyenneData[index], distanceMoyenneData),
                        normalizeValue(aireMinData[index], aireMinData),
                        normalizeValue(aireMaxData[index], aireMaxData),
                        normalizeValue(perimetreMoyenData[index], perimetreMoyenData),
                        normalizeValue(perimetreMinData[index], perimetreMinData),
                        normalizeValue(perimetreMaxData[index], perimetreMaxData)
                    ],
                    backgroundColor: `${colors[index].backgroundColor}B3`, // Ajout d'une transparence
                    borderColor: colors[index].borderColor,
                    borderWidth: 1
                }))
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const originalValues = [
                                    nbBatimentsData[context.dataIndex],
                                    aireMoyenneData[context.dataIndex],
                                    distanceMoyenneData[context.dataIndex],
                                    aireMinData[context.dataIndex],
                                    aireMaxData[context.dataIndex],
                                    perimetreMoyenData[context.dataIndex],
                                    perimetreMinData[context.dataIndex],
                                    perimetreMaxData[context.dataIndex]
                                ];
                                const originalValue = originalValues[context.rawIndex];
                                return `${context.dataset.label}: ${originalValue}`;
                            }
                        }
                    }
                },
                scales: {
                    r: {
                        beginAtZero: true,
                        suggestedMin: 0,
                        suggestedMax: 1
                    }
                }
            }
        });
    }

// Fonction pour normaliser une valeur dans un tableau donné
    function normalizeValue(value, dataset) {
        const min = Math.min(...dataset);
        const max = Math.max(...dataset);
        return (value - min) / (max - min);
    }

    // Fonction pour créer un radar chart
    function createRadarChart() {
        return new Chart(canvas.getContext('2d'), {
            type: 'radar',
            data: {
                labels: ['Nombre de bâtiments', 'Aire moyenne des bâtiments', 'Distance moyenne entre bâtiments', 'Aire minimale', 'Aire maximale', 'Périmètre moyen', 'Périmètre minimal', 'Périmètre maximal'],
                datasets: fileNames.map((fileName, index) => ({
                    label: fileName,
                    data: [
                        nbBatimentsData[index],
                        aireMoyenneData[index],
                        distanceMoyenneData[index],
                        aireMinData[index],
                        aireMaxData[index],
                        perimetreMoyenData[index],
                        perimetreMinData[index],
                        perimetreMaxData[index]
                    ],
                    backgroundColor: `${colors[index].backgroundColor}B3`, // Ajout d'une transparence
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


    // Ajoute un écouteur d'événement pour changer le type de graphique
    chartTypeElement.addEventListener('change', function () {
        if (chart) {
            chart.destroy(); // Détruit le graphique existant
        }

        // Crée un nouveau graphique en fonction du type sélectionné
        switch (chartTypeElement.value) {
            case 'classic':
                chart = createRadarChart();
                break;
            default:
                chart = createRadarChartNormalized();
                break;
        }
    });
});
