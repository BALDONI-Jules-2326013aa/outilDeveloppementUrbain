// Initialise la carte avec une vue centrée sur les coordonnées [0, 0] et un niveau de zoom de 5
var map = L.map('map').setView([0, 0], 5);

// Ajoute une couche de tuiles OpenStreetMap à la carte
L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

// Ajoute un écouteur d'événement pour le changement de fichier dans l'élément avec l'ID 'geotiff-file'
document.getElementById('geotiff-file').addEventListener('change', function(event) {
    // Récupère le fichier sélectionné par l'utilisateur
    var file = event.target.files[0];
    var reader = new FileReader();

    // Lit le fichier en tant que ArrayBuffer
    reader.readAsArrayBuffer(file);

    // Une fois la lecture terminée, traite le fichier
    reader.onloadend = function() {
        var arrayBuffer = reader.result;

        // Parse le GeoTIFF en utilisant la bibliothèque parseGeoraster
        parseGeoraster(arrayBuffer).then(georaster => {
            // Crée une nouvelle couche GeoRasterLayer avec le GeoTIFF parsé
            var layer = new GeoRasterLayer({
                georaster: georaster,
                opacity: 1,
                resolution: 256
            });

            // Ajoute la couche à la carte
            layer.addTo(map);

            // Ajuste les limites de la carte pour s'adapter à la nouvelle couche
            map.fitBounds(layer.getBounds());
        });
    };
});