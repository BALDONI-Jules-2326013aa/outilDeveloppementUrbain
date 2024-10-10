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



        echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            // On récupère les données json du $data
            const testGeoJSON = $this->data;
            

            // Initialiser la carte et la centrer
            const map = L.map('map');
            
            // Centrer la carte sur le premier point GeoJSON
            
            // Ajouter un fond de carte (par exemple, OpenStreetMap)
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 18,
                attribution: '&copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors'
            }).addTo(map);
            
            // On centre la carte sur les bounds
            map.fitBounds(L.geoJSON(testGeoJSON).getBounds());

            // Ajouter les points GeoJSON sur la carte
            L.geoJSON(testGeoJSON).addTo(map); 
        });
        
        </script>";
    }
}
