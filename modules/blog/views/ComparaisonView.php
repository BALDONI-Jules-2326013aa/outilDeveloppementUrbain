<?php

namespace blog\views;

use blog\models\GeoJSONModel;
use blog\models\TifModel;

class ComparaisonView extends AbstractView
{
    private string $body = '';

    protected function body(): void
    {
        include __DIR__ . "/Fragments/formulaireFichier.html";
        include __DIR__ . '/Fragments/comparaison.html';
        if (!is_readable($this->body)) {
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
            "<script src='_assets/scripts/map.js'></script>";

        $this->body .= $script;

    }

    public function afficherGraphiqueBatiments(array $dataArray, array $fileNames): void {
        $nbBatimentsJson = json_encode($dataArray);
        $fileNamesJson = json_encode($fileNames);

        $colorPickersHtml = '';
        foreach ($fileNames as $index => $fileName) {
            $colorPickersHtml .= "
        <div class='color-picker'>
            <label for='color_$index'>Couleur pour $fileName :</label>
            <input type='color' id='colorNbBatiments_$index' class='color-input' value='#" . substr(md5($fileName), 0, 6) . "'>
        </div>";
        }
        $graphique = "
        <div style='display: none;' id='nbBatimentsJson'>$nbBatimentsJson</div>
        <div style='display: none;' id='fileNamesJson'>$fileNamesJson</div>
        <div class='graphiqueBox' id='zoneNbBatiments'>
            <h2>Nombre de bâtiments par fichier</h2>
            <div class='mainContentGraph'>
                <div class='chart-options'>
                    <div>
                        <label for='chartTypeNbBatiments'>Choisir un type de graphique :</label>
                        <select id='chartTypeNbBatiments' class='combobox-chart'>
                            <option value='barChartNbBatiments' selected>Barres</option>
                            <option value='lineChartNbBatiments'>Ligne</option>
                            <option value='radarChartNbBatiments'>Radar</option>
                            <option value='polarChartNbBatiments'>Polaire</option>
                            <option value='doughnutChartNbBatiments'>Donut</option>
                            <option value='pieChartNbBatiments'>Camembert</option>
                        </select>
                    </div>
                    <div class='chart-colors'>
                        $colorPickersHtml
                    </div>
                </div>
                <div class='graphs'>
                    <canvas id='barBatiments'></canvas>
                </div>
            </div>
        </div>
        <script src='https://cdn.jsdelivr.net/npm/chart.js'></script>
        <script src='/_assets/scripts/nombreBatiments.js'></script>
        ";
        $this->body .= $graphique;
    }

    public function afficherGraphiqueRadarAireMoyenne(array $dataArray, array $fileNames): void {
        $aireMoyenneJson = json_encode($dataArray);
        $fileNamesJson = json_encode($fileNames);

        $colorPickersHtml = '';
        foreach ($fileNames as $index => $fileName) {
            $colorPickersHtml .= "
        <div class='color-picker'>
            <label for='colorAireMoyenne_$index'>Couleur pour $fileName :</label>
            <input type='color' id='colorAireMoyenne_$index' class='color-input' value='#" . substr(md5($fileName), 0, 6) . "'>
        </div>";
        }

        $graphique = "
    <div style='display: none;' id='aireMoyenneJson'>$aireMoyenneJson</div>
    <div style='display: none;' id='fileNamesJson'>$fileNamesJson</div>

    <div class='graphiqueBox' id='zoneAireMoyenne'>
        <h2>Aire moyenne par fichier</h2>
        <div class='mainContentGraph'>
            <div class='chart-options'>
                <div>
                    <label for='chartTypeAireMoyenne'>Choisir un type de graphique :</label>
                    <select id='chartTypeAireMoyenne' class='combobox-chart'>
                        <option value='barChartAireMoyenne' selected>Barres</option>
                        <option value='lineChartAireMoyenne'>Ligne</option>
                        <option value='radarChartAireMoyenne'>Radar</option>
                        <option value='polarChartAireMoyenne'>Polaire</option>
                        <option value='doughnutChartAireMoyenne'>Donut</option>
                        <option value='pieChartAireMoyenne'>Camembert</option>
                    </select>
                </div>
                <div class='chart-colors'>
                    $colorPickersHtml
                </div>
            </div>
            <div class='graphs'>
                <canvas id='radarAireMoyenne'></canvas>
            </div>
        </div>
    </div>
    <script src='https://cdn.jsdelivr.net/npm/chart.js'></script>
    <script src='/_assets/scripts/aireMoyenneBatiments.js'></script>
    ";

        $this->body .= $graphique;
    }

    public function afficherGraphiqueDistanceMoyenne(mixed $dataGeoJson, mixed $fileNamesGeojson): void
    {
        $distanceMoyenneJson = json_encode($dataGeoJson);
        $fileNamesJson = json_encode($fileNamesGeojson);

        $colorPickersHtml = '';
        foreach ($fileNamesGeojson as $index => $fileName) {
            $colorPickersHtml .= "
        <div class='color-picker'>
            <label for='colorDistanceMoyenne_$index'>Couleur pour $fileName :</label>
            <input type='color' id='colorDistanceMoyenne_$index' class='color-input' value='#" . substr(md5($fileName), 0, 6) . "'>
        </div>";
        }

        $graphique =  "
        <div style='display: none;' id='distanceMoyenneJson'>$distanceMoyenneJson</div>
        <div style='display: none;' id='fileNamesJson'>$fileNamesJson</div>
    
        <div class='graphiqueBox' id='zoneDistanceMoyenne'>
            <h2>Distance moyenne entre bâtiments</h2>
            <div class='mainContentGraph'>
                <div class='chart-options'>
                    <div>
                        <label for='chartTypeDistanceMoyenne'>Choisir un type de graphique :</label>
                        <select id='chartTypeDistanceMoyenne' class='combobox-chart'>
                            <option value='barChartDistanceMoyenne' selected>Barres</option>
                            <option value='lineChartDistanceMoyenne'>Ligne</option>
                            <option value='radarChartDistanceMoyenne'>Radar</option>
                            <option value='polarChartDistanceMoyenne'>Polaire</option>
                            <option value='doughnutChartDistanceMoyenne'>Donut</option>
                            <option value='pieChartDistanceMoyenne'>Camembert</option>
                        </select>
                    </div>
                    <div class='chart-colors'>
                        $colorPickersHtml
                    </div>
                </div>
                <div class='graphs'>
                    <canvas id='barDistanceMoyenne'></canvas>
                </div>
            </div>
        </div>
        <script src='https://cdn.jsdelivr.net/npm/chart.js'></script>
        <script src='/_assets/scripts/distanceMoyenneBatiments.js'></script>
        ";
        $this->body .= $graphique;

    }

    public function afficherGraphiqueRecap(array $nbBatimentsArray, array $aireMoyenneArray, array $distanceMoyenneArray, array $fileNames): void
    {
        $nbBatimentsJson = json_encode($nbBatimentsArray);
        $aireMoyenneJson = json_encode($aireMoyenneArray);
        $distanceMoyenneJson = json_encode($distanceMoyenneArray);
        $fileNamesJson = json_encode($fileNames);

        $colorPickersHtml = '';
        foreach ($fileNames as $index => $fileName) {
            $colorPickersHtml .= "
        <div class='color-picker'>
            <label for='colorRecap_$index'>Couleur pour $fileName :</label>
            <input type='color' id='colorRecap_$index' class='color-input' value='#" . substr(md5($fileName), 0, 6) . "'>
        </div>";
        }

        $graphique = "
        <div style='display: none;' id='nbBatimentsJson'>$nbBatimentsJson</div>
        <div style='display: none;' id='aireMoyenneJson'>$aireMoyenneJson</div>
        <div style='display: none;' id='distanceMoyenneJson'>$distanceMoyenneJson</div>
        <div style='display: none;' id='fileNamesJson'>$fileNamesJson</div>
    
        <div class='graphiqueBox' id='zoneRecap'>
            <h2>Récapitulatif</h2>
            <div class='mainContentGraph'>
                <div class='chart-options'>
                    <div>
                        <label for='chartTypeRecap'>Choisir un type de graphique :</label>
                        <select id='chartTypeRecap' class='combobox-chart'>
                            <option value='barChartRecap' selected>Barres</option>
                            <option value='lineChartRecap'>Ligne</option>
                            <option value='radarChartRecap'>Radar</option>
                            <option value='polarChartRecap'>Polaire</option>
                            <option value='doughnutChartRecap'>Donut</option>
                            <option value='pieChartRecap'>Camembert</option>
                        </select>
                    </div>
                    <div class='chart-colors'>
                        $colorPickersHtml
                    </div>
                </div>
                <div class='graphs'>
                    <canvas id='barRecap'></canvas>
                </div>
            </div>
        </div>
        <script src='https://cdn.jsdelivr.net/npm/chart.js'></script>
        <script src='/_assets/scripts/recapChart.js'></script>
        ";
        $this->body .= $graphique;
    }

    public function afficheTif(array $dataArray): void {
        $tifModel = new TifModel();
        $htmlOutput = '';

        foreach ($dataArray as $tifFile) {
            $htmlOutput .= $tifModel->visualisationHillShade($tifFile);
        }

        $this->body .= $htmlOutput;
    }

    public function afficher(): void
    {
        parent::afficher();
    }
}
