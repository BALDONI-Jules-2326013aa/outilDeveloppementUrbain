// Initialise la carte avec les coordonnées et le niveau de zoom
var map = L.map('map').setView([48.8566, 2.3522], 13); // Paris en exemple

// Ajoute une couche de tuiles (OpenStreetMap)
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

// Fonction pour charger et afficher le GeoJSON
function loadGeoJSON(file = 'data/carte.geojson') {
    fetch('analyseController.php?file=' + file)
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            console.log(data.error); // Afficher le message d'erreur s'il n'y a pas de fichier
            document.getElementById('uploadStatus').innerHTML = data.error;
        } else {
            // Ajoute les données GeoJSON à la carte
            L.geoJSON(data).addTo(map);
        }
    })
    .catch(error => {
        console.error('Erreur lors du chargement du GeoJSON:', error);
    });
}

// Gestion du formulaire de téléchargement
document.getElementById('uploadForm').addEventListener('submit', function (e) {
    e.preventDefault(); // Empêche le rechargement de la page
    
    var formData = new FormData(this);

    fetch('analyseController.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('uploadStatus').innerHTML = "Fichier téléchargé avec succès";
            // Charge le nouveau fichier GeoJSON sur la carte
            loadGeoJSON(data.filename);
        } else {
            document.getElementById('uploadStatus').innerHTML = "Erreur : " + data.error;
        }
    })
    .catch(error => {
        console.error('Erreur lors de l\'upload:', error);
        document.getElementById('uploadStatus').innerHTML = "Erreur lors de l'upload du fichier";
    });
});
