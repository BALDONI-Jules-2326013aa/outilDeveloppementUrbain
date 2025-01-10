<?php

namespace blog\views;

use blog\models\GeoJSONModel;
use blog\models\TifModel;

class ComparaisonView extends AbstractView
{
    private string $body = '';

    // Affiche le corps de la page
    protected function body(): void
    {
        // Inclut le formulaire de téléchargement de fichiers
        include __DIR__ . "/Fragments/formulaireFichier.html";
        // Inclut le fichier de comparaison
        include __DIR__ . '/Fragments/comparaison.html';
        // Affiche le contenu du corps si lisible
        if (!is_readable($this->body)) {
            echo $this->body;
        }
    }

    // Retourne le nom du fichier CSS spécifique à cette vue
    function css(): string
    {
        return 'comparaison.css';
    }

    // Retourne le titre de la page
    function pageTitle(): string
    {
        return 'Comparaison';
    }

    // Affiche la page avec les fichiers GeoJSON
    public function afficherAvecFichiers(array $dataArray, array $fileNames): void
    {
        // Encode les données GeoJSON et les noms de fichiers en JSON
        $geojsonDataJsArray = json_encode($dataArray);
        $fileNamesJsArray = json_encode($fileNames);

        // Crée le script pour inclure Leaflet et les données GeoJSON
        $script =  "<link rel='stylesheet' href='https://unpkg.com/leaflet@1.9.4/dist/leaflet.css' />" .
            "<script src='https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'></script>" .
            "<script>
            const geojsonDataArray = $geojsonDataJsArray;
            const fileNamesArray = $fileNamesJsArray;
          </script>" .
            "<script src='_assets/scripts/map.js'></script>";

        // Ajoute le script au corps de la page
        $this->body .= $script;
    }

    // Affiche le graphique du nombre de bâtiments par fichier
    public function afficherGraphiqueBatiments(array $dataArray, array $fileNames): void {
        // Encode les données et les noms de fichiers en JSON
        $nbBatimentsJson = json_encode($dataArray);
        $fileNamesJson = json_encode($fileNames);

        // Génère les sélecteurs de couleur pour chaque fichier
        $colorPickersHtml = '';
        foreach ($fileNames as $index => $fileName) {
            $colorPickersHtml .= "
        <div class='color-picker'>
            <label for='color_$index'>Couleur pour $fileName :</label>
            <input type='color' id='colorNbBatiments_$index' class='color-input' value='#" . substr(md5($fileName), 0, 6) . "'>
        </div>";
        }

        // Crée le HTML pour le graphique du nombre de batiments
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

        // Ajoute le graphique au corps de la page
        $this->body .= $graphique;
    }

    // Affiche le graphique radar de l'aire moyenne par fichier
    public function afficherGraphiqueRadarAireMoyenne(array $dataArray, array $fileNames): void {
        // Encode les données et les noms de fichiers en JSON
        $aireMoyenneJson = json_encode($dataArray);
        $fileNamesJson = json_encode($fileNames);

        // Génère les sélecteurs de couleur pour chaque fichier
        $colorPickersHtml = '';
        foreach ($fileNames as $index => $fileName) {
            $colorPickersHtml .= "
        <div class='color-picker'>
            <label for='colorAireMoyenne_$index'>Couleur pour $fileName :</label>
            <input type='color' id='colorAireMoyenne_$index' class='color-input' value='#" . substr(md5($fileName), 0, 6) . "'>
        </div>";
        }

        // Crée le HTML pour le graphique de l'aire moyenne
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

        // Ajoute le graphique au corps de la page
        $this->body .= $graphique;
    }

    // Affiche le graphique de la distance moyenne entre bâtiments
    public function afficherGraphiqueDistanceMoyenne(mixed $dataGeoJson, mixed $fileNamesGeojson): void
    {
        // Encode les données et les noms de fichiers en JSON
        $distanceMoyenneJson = json_encode($dataGeoJson);
        $fileNamesJson = json_encode($fileNamesGeojson);

        // Génère les sélecteurs de couleur pour chaque fichier
        $colorPickersHtml = '';
        foreach ($fileNamesGeojson as $index => $fileName) {
            $colorPickersHtml .= "
        <div class='color-picker'>
            <label for='colorDistanceMoyenne_$index'>Couleur pour $fileName :</label>
            <input type='color' id='colorDistanceMoyenne_$index' class='color-input' value='#" . substr(md5($fileName), 0, 6) . "'>
        </div>";
        }

        // Crée le HTML pour le graphique de la distance moyenne
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

        // Ajoute le graphique au corps de la page
        $this->body .= $graphique;
    }

    // Affiche le graphique récapitulatif des aires minimales et maximales
    public function afficherGraphiqueRecap(array $aireMin, array $aireMax, array $fileNames): void
    {
        // Encode les données en JSON
        $aireMinJson = json_encode($aireMin);
        $aireMaxJson = json_encode($aireMax);

        // Journalise les données pour le débogage
        echo '<script>console.log("aireMin dans la view: ' . $aireMin . '")</script>';
        echo '<script>console.log("aireMax dans la view: ' . $aireMax . '")</script>';

        // Génère les sélecteurs de couleur pour chaque fichier
        $colorPickersHtml = '';
        foreach ($fileNames as $index => $fileName) {
            $colorPickersHtml .= "
        <div class='color-picker'>
            <label for='colorRecap_$index'>Couleur pour $fileName :</label>
            <input type='color' id='colorRecap_$index' class='color-input' value='#" . substr(md5($fileName), 0, 6) . "'>
        </div>";
        }

        // Crée le HTML pour le graphique récapitulatif
        $graphique = "
        <div style='display: none;' id='aireMinJson'>$aireMinJson</div>
        <div style='display: none;' id='aireMaxJson'>$aireMaxJson</div>
        <div class='graphiqueBox' id='zoneRecap'>
            <h2>Récapitulatif</h2>
            <div class='mainContentGraph'>
                <div class='chart-options'>
                    <div>
                        <label for='chartTypeRecap'>Choisir un type de graphique :</label>
                        <select id='chartTypeRecap' class='combobox-chart'>
                            <option value='barChartRecap' selected>Barres</option>
                            <option value='radarChartRecap'>Radar</option>
                        </select>
                    </div>
                    <div class='chart-colors'>
                        $colorPickersHtml
                    </div>
                </div>
                <div class='graphs'>
                    <canvas id='recapChartCanva'></canvas>
                </div>
            </div>
        </div>
        <script src='https://cdn.jsdelivr.net/npm/chart.js'></script>
        <script src='/_assets/scripts/recapChart.js'></script>
        ";

        // Ajoute le graphique récapitulatif au corps de la page
        $this->body .= $graphique;
    }

    // Affiche les visualisations Hillshade pour les fichiers TIFF
    public function afficheTif(array $dataArray): void {
        $tifModel = new TifModel();
        $htmlOutput = '';

        // Génère la visualisation Hillshade pour chaque fichier TIFF
        foreach ($dataArray as $tifFile) {
            $htmlOutput .= $tifModel->visualisationHillShade($tifFile);
        }

        // Ajoute les visualisations au corps de la page
        $this->body .= $htmlOutput;
    }

    // Affiche la page complète
    public function afficher(): void
    {
        parent::afficher();
    }
}