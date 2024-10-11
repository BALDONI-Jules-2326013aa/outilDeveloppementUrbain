<?php

namespace blog\views;

class AnalyseView extends AbstractView
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function css(): string
    {
        return 'analyse.css';
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


        echo "
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const testGeoJSON = json_encode($this->data['geojson']);
                
                const map = L.map('map');
                
                // Ajouter un fond de carte (par exemple, OpenStreetMap)
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 18,
                    attribution: '&copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors'
                }).addTo(map);
                
                map.fitBounds(L.geoJSON(testGeoJSON).getBounds());
    
                L.geoJSON(testGeoJSON).addTo(map); 
            });
        
        </script>";
    }
}
