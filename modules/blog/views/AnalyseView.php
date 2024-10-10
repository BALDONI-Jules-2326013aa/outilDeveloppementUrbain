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
            const data = JSON.parse('$this->data');

            // Initialiser la carte
            const map = L.map('map');

            L.geoJSON(data).addTo(map); 
        });
        
        </script>";
    }
}
