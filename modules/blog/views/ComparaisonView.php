<?php

namespace blog\views;
use blog\models\GeoJSONModel;
use blog\models\TifModel;

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

        $this->body .= $script;
    }

    public function afficherGraphiqueBatiments(array $dataArray, array $fileNames): void {
        $geoJsonModel = new GeoJSONModel();
        $nbBatiments = $geoJsonModel->recupereNombreBatiment($dataArray);
        $script = $geoJsonModel->dessineGraphiqueNombreBatiments($nbBatiments, $fileNames);
        $this->body .= $script;
    }

    public function afficherGraphiqueRadarAireMoyenne(array $dataArray, array $fileNames): void {
        $geoJsonModel = new GeoJSONModel();
        $surfaceMoyenne = $geoJsonModel->recupereSurfaceMoyenneBatiments($dataArray);
        foreach ($surfaceMoyenne as $key => $value) {
            $surfaceMoyenne[$key] = 1000000000 * $value;
        }
        $script = $geoJsonModel->dessineGraphiqueRadarAireMoyenne($surfaceMoyenne, $fileNames);
        $this->body .= $script;
    }

    public function afficheImageTifSurCarte(array $dataArray): void {
        $tifModel = new TifModel();
        $htmlOutput = '';

        foreach ($dataArray as $tifFile) {
            $htmlOutput .= $tifModel->visualisationHillShade($tifFile);
        }

        $this->body .= $htmlOutput; // Ajoute la sortie HTML à la vue
    }



    public function afficher(): void
    {
        parent::afficher();
    }
}
