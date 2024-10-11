<?php

namespace blog\views;

use http\Message\Body;

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
        return  'comparaison.css';
    }

    function pageTitle(): string
    {
        return 'Comparaison';
    }

    public function afficherAvecFichier($data): void
    {
        // Assure-toi que la chaîne GeoJSON est transmise correctement
        $geojsonData = json_encode($data);  // Encode correctement la chaîne JSON en PHP

        $script =
            "<link rel='stylesheet' href='https://unpkg.com/leaflet@1.9.4/dist/leaflet.css' />" .
            "<script src='https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'></script>" .
            "<div id='map' style='height: 500px;'></div>" .
            "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Parse la chaîne GeoJSON dans un objet JavaScript
                    const testGeoJSON = JSON.parse($geojsonData);
                    
                    const map = L.map('map').setView([0, 0], 2);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 18,
                        attribution: '&copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors'
                    }).addTo(map);

                    // Créer la couche GeoJSON et ajuster la carte aux données
                    const geoJsonLayer = L.geoJSON(testGeoJSON).addTo(map);
                    map.fitBounds(geoJsonLayer.getBounds());
                });
            </script>";

        // Affecter le script au body
        $this->body = $script;
        parent::afficher();
    }


    public function afficher(): void
    {
        parent::afficher();
    }
}