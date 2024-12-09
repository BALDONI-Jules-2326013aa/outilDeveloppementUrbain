<?php

namespace blog\views;

class SimulationView extends AbstractView
{
    private $body;

    public function __construct() {
        $this->body = '';
    }

    public function css(): string
    {
        return 'simulation.css';
    }

    public function pageTitle(): string
    {
        return 'SimulationController';
    }

    protected function body(): void
    {
        if (is_readable($this->body)) {
            include $this->body;
        } else {
            include __DIR__ . '/Fragments/simulation.html';
            echo $this->body;
        }
    }

    public function afficherGetYears(array $fileYears, array $fileNames): void
    {

        if (count($fileYears) > 0) {
            $simulation = "<h2>Simulation</h2>";
        } else {
            $simulation = "<h2>Simulation</h2><p>Aucun fichier n'a été chargé</p>";
        }

        foreach ($fileYears as $key => $fileYear) {
            $simulation .= "<form id='yearsForm' method='post' action='/startSimulation'>
            <label for='year'>Année du fichier $fileNames[$key] : </label>
            <input type='number' id='year' name='year' min='0' value='$fileYear' required>";
        }

        if (count($fileYears) > 0) {
            $simulation .= "<input type='submit' value='Lancer la simulation'></form>";
        }

        $this->body = $simulation;

    }

    public function resultatSimulation(array $dataArray, array $fileNames): void
{
        $geojsonDataJsArray = json_encode($dataArray);
        $fileNamesJsArray = json_encode($fileNames);

        $script =  "<link rel='stylesheet' href='https://unpkg.com/leaflet@1.9.4/dist/leaflet.css' />" .
            "<script src='https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'></script>" .
            "<script>
            const geojsonDataArray = $geojsonDataJsArray;
            const fileNamesArray = $fileNamesJsArray;
          </script>" .
            "<script src='_assets/scripts/simulation.js'></script>";

        $this->body = $script;

    }


    public function afficher(): void
    {
        parent::afficher();
    }
}
