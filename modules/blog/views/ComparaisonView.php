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
            "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    const map = L.map('map').setView([0, 0], 2);
                    
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 18,
                        attribution: '&copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors'
                    }).addTo(map);

                    // Afficher les données GeoJSON pour chaque fichier
                    const geojsonDataArray = $geojsonDataJsArray;
                    console.log('Données GeoJSON chargées:', geojsonDataArray); // Vérifier les données dans la console

                    geojsonDataArray.forEach(function(geojsonData) {
                        try {
                            if (geojsonData && geojsonData.type === 'FeatureCollection') {
                                // On ajoute à la carte avec une couleur différente
                                const geoJsonLayer = L.geoJSON(geojsonData).addTo(map);
                                map.fitBounds(geoJsonLayer.getBounds()); 
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
