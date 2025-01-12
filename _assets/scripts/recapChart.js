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

    console.log("fileNames", fileNames);
    console.log("perimetreMinData", perimetreMinData);
    console.log("perimetreMaxData", perimetreMaxData);
    console.log("perimetreMinData normalized", perimetreMinData.map((value, index) => normalizeValue(value, perimetreMinData)));
    console.log("perimetreMaxData normalized", perimetreMaxData.map((value, index) => normalizeValue(value, perimetreMaxData)));

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
    createRadarChartNormalized();

    function createRadarChartNormalized() {
        // Normalisation indépendante pour chaque axe
        const normalizedData = {
            nbBatimentsData: nbBatimentsData.map(value => value / Math.max(...nbBatimentsData)),
            aireMoyenneData: aireMoyenneData.map(value => value / Math.max(...aireMoyenneData)),
            distanceMoyenneData: distanceMoyenneData.map(value => value / Math.max(...distanceMoyenneData)),
            aireMinData: aireMinData.map(value => value / Math.max(...aireMinData)),
            aireMaxData: aireMaxData.map(value => value / Math.max(...aireMaxData)),
            perimetreMoyenData: perimetreMoyenData.map(value => value / Math.max(...perimetreMoyenData)),
            perimetreMinData: perimetreMinData.map(value => value / Math.max(...perimetreMinData)),
            perimetreMaxData: perimetreMaxData.map(value => value / Math.max(...perimetreMaxData))
        };

        // Créer le radar chart
        return new Chart(canvas.getContext('2d'), {
            type: 'radar',
            data: {
                labels: [
                    `Nombre de bâtiments (max: ${Math.max(...nbBatimentsData).toFixed(2)})`,
                    `Aire moyenne (max: ${Math.max(...aireMoyenneData).toFixed(2)})`,
                    `Distance moyenne (max: ${Math.max(...distanceMoyenneData).toFixed(2)})`,
                    `Aire minimale (max: ${Math.max(...aireMinData).toFixed(2)})`,
                    `Aire maximale (max: ${Math.max(...aireMaxData).toFixed(2)})`,
                    `Périmètre moyen (max: ${Math.max(...perimetreMoyenData).toFixed(2)})`,
                    `Périmètre minimal (max: ${Math.max(...perimetreMinData).toFixed(2)})`,
                    `Périmètre maximal (max: ${Math.max(...perimetreMaxData).toFixed(2)})`
                ],
                datasets: fileNames.map((fileName, index) => ({
                    label: fileName,
                    data: [
                        normalizedData.nbBatimentsData[index],
                        normalizedData.aireMoyenneData[index],
                        normalizedData.distanceMoyenneData[index],
                        normalizedData.aireMinData[index],
                        normalizedData.aireMaxData[index],
                        normalizedData.perimetreMoyenData[index],
                        normalizedData.perimetreMinData[index],
                        normalizedData.perimetreMaxData[index]
                    ],
                    backgroundColor: `${colors[index].backgroundColor}55`, // Semi-transparent
                    borderColor: colors[index].borderColor,
                    borderWidth: 2, // Finesse des lignes
                    pointBackgroundColor: '#fff', // Points blancs au centre
                    pointBorderColor: colors[index].borderColor,
                    pointBorderWidth: 2, // Points nets
                    pointRadius: 4 // Points plus visibles
                }))
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                // Afficher les valeurs originales
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
                                return `${context.dataset.label}: ${originalValue.toFixed(2)}`;
                            }
                        }
                    }
                },
                scales: {
                    r: {
                        ticks: {
                            beginAtZero: true,
                            display: true,
                            stepSize: 0.2 // Régle les étapes en fonction de la normalisation
                        },
                        pointLabels: {
                            font: {
                                size: 14, // Taille légèrement augmentée
                                weight: 'bold' // Texte des axes plus visible
                            },
                            color: '#333'
                        },
                        grid: {
                            color: '#ddd' // Grille plus claire
                        },
                        angleLines: {
                            display: true,
                            color: '#bbb' // Lignes des angles adoucies
                        }
                    }
                }
            }
        });
    }


    // Fonction pour normaliser une valeur par rapport à la somme totale d'un dataset
    function normalizeValue(value, dataset) {
        const sum = dataset.reduce((acc, val) => acc + val, 0); // Somme totale du dataset
        return value / sum; // Retourne la proportion
    }

});
