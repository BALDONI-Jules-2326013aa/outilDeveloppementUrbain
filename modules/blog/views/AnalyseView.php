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

        // Assure-toi que les données sont bien encodées en JSON
        $geojsonData = json_encode(json_decode($this->data, true));

        echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            const geojsonData = $geojsonData;

            // Initialise la carte Leaflet sans couche de fond
            const map = L.map('map', {
                center: [0, 0], // Centre initial, ajuste selon tes besoins
                zoom: 2, // Zoom initial
                zoomControl: true,
                attributionControl: false
            });

            // Ajout des données GeoJSON
            const geoLayer = L.geoJSON(JSON.parse(geojsonData)).addTo(map);

            // Ajuste la vue de la carte en fonction des données GeoJSON
            map.fitBounds(geoLayer.getBounds());
        });
    </script>";
    }



}
