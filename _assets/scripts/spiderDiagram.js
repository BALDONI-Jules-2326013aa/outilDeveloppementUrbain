document.addEventListener('DOMContentLoaded', function() {
    let aireJsonElement = document.getElementById('aireJson');
    if (!aireJsonElement) {
        console.error("Element with id 'aireJson' not found.");
        return;
    }

    let aireJsonText = aireJsonElement.textContent;
    if (!aireJsonText) {
        console.error("Element with id 'aireJson' has no text content.");
        return;
    }

    let Aire;
    try {
        Aire = JSON.parse(aireJsonText);
    } catch (e) {
        console.error("Failed to parse JSON from 'aireJson':", e);
        return;
    }

    let fileNamesJsonElement = document.getElementById('fileNamesJson');
    if (!fileNamesJsonElement) {
        console.error("Element with id 'fileNamesJson' not found.");
        return;
    }

    let fileNamesJsonText = fileNamesJsonElement.textContent;
    if (!fileNamesJsonText) {
        console.error("Element with id 'fileNamesJson' has no text content.");
        return;
    }

    let fileNames;
    try {
        fileNames = JSON.parse(fileNamesJsonText);
    } catch (e) {
        console.error("Failed to parse JSON from 'fileNamesJson':", e);
        return;
    }

    const ctx = document.getElementById('spiderDiagram').getContext('2d');
    const data = {
        labels: ['Aire Moyenne', 'Aire Min', 'Aire Max'],
        datasets: Aire.map((aire, index) => ({
            label: fileNames[index],
            data: [aire.avg_area, aire.min_area, aire.max_area],
            fill: true,
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgb(54, 162, 235)',
            pointBackgroundColor: 'rgb(54, 162, 235)',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: 'rgb(54, 162, 235)'
        }))
    };

    const option = {
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

    new Chart(ctx, option);
});