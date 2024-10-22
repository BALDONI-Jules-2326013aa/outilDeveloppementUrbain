<?php

namespace blog\views;
use blog\models\GeoJSONModel;

class ComparaisonView extends AbstractView
{
    private string $body = __DIR__ . '/Fragments/comparaison.html';

    protected function body(): void
    {
        if (is_readable($this->body)) {
            include $this->body;
        } else {
            include __DIR__ . '/Fragments/comparaison.html';
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

    public function afficherAvecFichiers(array $dataArray, array $fileNames): void
    {
        $geojsonDataJsArray = json_encode($dataArray);
        $fileNamesJsArray = json_encode($fileNames);

        $script =  "<link rel='stylesheet' href='https://unpkg.com/leaflet@1.9.4/dist/leaflet.css' />" .
            "<script src='https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'></script>" .
            "<script>
            const geojsonDataArray = $geojsonDataJsArray;
            const fileNamesArray = $fileNamesJsArray;
          </script>" .
            "<script src='_assets/scripts/comparaison.js'></script>";

        $this->body = $script;


    }

    public function afficherGraphiqueBatiments(array $dataArray, array $fileNames): void {
        $geoJsonModel = new GeoJSONModel();
        $nbBatiments = $geoJsonModel->recupereNombreBatiment($dataArray);
        $script = $geoJsonModel->dessineGraphiqueNombreBatiments($nbBatiments, $fileNames);

        $this->body.= $script;
    }

    public function afficherGraphiqueAireBatiments(array $dataArray, array $fileNames): void {
        $geoJsonModel = new GeoJSONModel();
        $aireBatiments = $geoJsonModel->recupereSurfaceTotale($dataArray);
        $script = $geoJsonModel->dessineGraphiqueAireBatiments($aireBatiments, $fileNames);

        $this->body.= $script;
    }

    public function afficher(): void
    {
        parent::afficher();
    }
}
