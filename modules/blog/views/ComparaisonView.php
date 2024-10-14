<?php

namespace blog\views;

class ComparaisonView extends AbstractView
{
    private string $body = __DIR__ . '/Fragments/comparaison.html';

    protected function body(): void
    {
        if (is_readable($this->body)) {
            include $this->body;
        } else {
            echo $this->body;
        }
    }

    function css(): string
    {
        return 'comparaison.css';
    }

    function pageTitle(): string
    {
        return 'Comparaison';
    }

    public function afficherAvecFichiers(array $dataArray): void
    {
        // Encode les données GeoJSON pour les passer au script JavaScript
        $geojsonDataJsArray = json_encode($dataArray);

        $script =
            "<link rel='stylesheet' href='https://unpkg.com/leaflet@1.9.4/dist/leaflet.css' />" .
            "<script src='https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'></script>" .
            "<div id='map' style='height: 500px;'></div>" .
            "<div id='color-selectors'></div>" . // Conteneur pour les sélecteurs de couleur
            "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    const map = L.map('map').setView([0, 0], 2);
                    
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 18,
                        zoomControl: true,
                        attribution: '&copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors'
                    }).addTo(map);

                    // Convertir les données GeoJSON pour chaque fichier
                    const geojsonDataArray = $geojsonDataJsArray;
                    const layers = []; // Stocke chaque couche GeoJSON ajoutée
                    
                    geojsonDataArray.forEach(function(geojsonData, index) {
                        try {
                            if (geojsonData && geojsonData.type === 'FeatureCollection') {
                                let color = '#' + Math.floor(Math.random() * 16777215).toString(16);
                                const layer = L.geoJSON(geojsonData, {
                                    style: function(feature) {
                                        return { color: color };
                                    }
                                }).addTo(map);

                                layers.push(layer);
                                map.fitBounds(layer.getBounds());

                                // Créer un sélecteur de couleur pour ce fichier
                                const colorSelector = document.createElement('input');
                                colorSelector.type = 'color';
                                colorSelector.value = color;
                                colorSelector.addEventListener('change', function() {
                                    layer.setStyle({ color: colorSelector.value });
                                });

                                // Ajouter le sélecteur de couleur dans le conteneur
                                const label = document.createElement('label');
                                label.textContent = 'Couleur pour le fichier ' + (index + 1) + ': ';
                                label.appendChild(colorSelector);
                                document.getElementById('color-selectors').appendChild(label);
                            } else {
                                console.warn('Fichier GeoJSON vide ou structure invalide');
                            }
                        } catch (error) {
                            console.error('Erreur lors de l\'ajout du GeoJSON:', error);
                        }
                    });
                });
            </script>";

        // Affecter le script au body pour exécution
        $this->body = $script;
        parent::afficher();
    }

    public function afficher(): void
    {
        parent::afficher();
    }
}
