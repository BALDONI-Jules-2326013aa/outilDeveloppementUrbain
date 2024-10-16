document.addEventListener('DOMContentLoaded', function() {
    let ctx = document.getElementById('barBatiments').getContext('2d');
    let data = {
        labels: JSON.parse(document.getElementById('fileNamesJson').textContent),
        datasets: [{
            label: 'Nombre de bâtiments',
            data: JSON.parse(document.getElementById('nbBatimentsJson').textContent),
            backgroundColor: '#6b5eba',
            borderColor: '#557002',
            borderWidth: 1
        }]
    };

    let barBatiments = new Chart(ctx, {
        type: 'bar',
        data: data,
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
