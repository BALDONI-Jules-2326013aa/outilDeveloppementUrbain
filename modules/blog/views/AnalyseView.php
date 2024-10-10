<?php

namespace blog\views;

class AnalyseView extends AbstractView
{
    private string $data;

    public function __construct(string $data)
    {
        $this->data = $data;
    }

    public function css(): string
    {
        return 'styles.css';
    }

    public function pageTitle(): string
    {
        return 'Analyse Page';
    }

    protected function body()
    {
        include __DIR__ . '/Fragments/analyse.html';

        echo "<link rel='stylesheet' href='https://unpkg.com/leaflet@1.9.4/dist/leaflet.css' />";
        echo "<script src='https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'></script>";

        echo "<div id='map' style='width: 600px; height: 400px;'></div>"; // Ajouter une div pour la carte

        echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            // Définition de l'objet GeoJSON avec les guillemets appropriés
            const testGeoJSON = {
                \"type\": \"FeatureCollection\",
                \"features\": [
                    {
                        \"type\": \"Feature\",
                        \"properties\": {
                            \"name\": \"Point 1\"
                        },
                        \"geometry\": {
                            \"type\": \"Point\",
                            \"coordinates\": [0, 0]
                        }
                    },
                    {
                        \"type\": \"Feature\",
                        \"properties\": {
                            \"name\": \"Point 2\"
                        },
                        \"geometry\": {
                            \"type\": \"Point\",
                            \"coordinates\": [1, 1]
                        }
                    }
                ]
            };

            // Initialiser la carte et la centrer
            const map = L.map('map').setView([0.5, 0.5], 2);

            // Ajouter un fond de carte (par exemple, OpenStreetMap)
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 18,
                attribution: '&copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Ajouter les points GeoJSON sur la carte
            L.geoJSON(testGeoJSON).addTo(map); 
        });
        
        </script>";
    }
}
