<?php

namespace blog\views;

use blog\models\GeoJSONModel;
use blog\models\TifModel;

class ComparaisonView extends AbstractView
{
    private string $body = '';

    /**
     * Affiche le corps de la page.
     * @return void
     */
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

    /**
     * Retourne le nom du fichier CSS spécifique à cette vue.
     * @return string Le nom du fichier CSS.
     */
    function css(): string
    {
        return 'comparaison.css';
    }

    /**
     * Retourne le titre de la page.
     * @return string Le titre de la page.
     */
    function pageTitle(): string
    {
        return 'Comparaison';
    }

    function afficherCRS(string $crs, bool $erreur) {
        $crsBox = "<div id='crs' style='display: none;'>$crs</div>
        ";
        if ($erreur) {
            $crsBox .= "<p>Attention, les fichiers GeoJSON ne sont pas sous le même système de coordonnées ! Cela peut entraîner des erreurs d'affichage.</p>";
        }
        $this->body .= $crsBox;
    }


    /**
     * Affiche la page avec les fichiers GeoJSON.
     * @param array $dataArray Les données GeoJSON.
     * @param array $fileNames Les noms des fichiers GeoJSON.
     * @return void
     */
    public function afficherAvecFichiers(array $dataArray, array $fileNames): void
    {
        // Encode les données GeoJSON et les noms de fichiers en JSON
        $geojsonDataJsArray = json_encode($dataArray);
        $fileNamesJsArray = json_encode($fileNames);

        // Crée le script pour inclure Leaflet et les données GeoJSON
        $script =  "<link rel='stylesheet' href='https://unpkg.com/leaflet@1.9.4/dist/leaflet.css' />" .
            "<script src='https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.8.1/proj4.js'></script>" .
            "<script src='https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'></script>" .
            "<script src='https://cdnjs.cloudflare.com/ajax/libs/proj4leaflet/1.0.2/proj4leaflet.js'></script>" .
            "<script>
            const geojsonDataArray = $geojsonDataJsArray;
            const fileNamesArray = $fileNamesJsArray;
          </script>" .
            "<script src='_assets/scripts/map.js'></script>";

        // Ajoute le script au corps de la page
        $this->body .= $script;
    }

    /**
     * Affiche le graphique récapitulatif des aires minimales et maximales.
     * @param array $aireMin Les aires minimales.
     * @param array $aireMax Les aires maximales.
     * @param array $fileNames Les noms des fichiers.
     * @return void
     */
    public function afficherGraphiqueRecap(array $nbBatiments, array $airesMoyennes, array $distanceMoyenne, array $aireMin, array $aireMax, array $fileNames): void
    {
        // Encode les données en JSON
        $fileNamesJson = json_encode($fileNames);
        $aireMinJson = json_encode($aireMin);
        $aireMaxJson = json_encode($aireMax);
        $distanceMoyenneJson = json_encode($distanceMoyenne);
        $aireMoyenneJson = json_encode($airesMoyennes);
        $nbBatimentsJson = json_encode($nbBatiments);

        // Crée le HTML pour le graphique récapitulatif
        $graphique = "
    <div style='display: none;' id='fileNamesJson'>$fileNamesJson</div>
        <div style='display: none;' id='aireMinJson'>$aireMinJson</div>
        <div style='display: none;' id='aireMaxJson'>$aireMaxJson</div>
        <div style='display: none;' id='distanceMoyenneJson'>$distanceMoyenneJson</div>
        <div style='display: none;' id='aireMoyenneJson'>$aireMoyenneJson</div>
        <div style='display: none;' id='nbBatimentsJson'>$nbBatimentsJson</div>
        <div class='graphiqueBox' id='zoneRecap'>
            <h2>Récapitulatif</h2>
            <div class='mainContentGraph'>
                <div>
                    <label for='chartTypeRecap'>Choisir un type de graphique :</label>
                    <select id='chartTypeRecap' class='combobox-chart'>
                        <option value='normalized' selected>Normalisé</option>
                        <option value='classic'>Non normalisé</option>
                    </select>
                </div>
                <canvas id='recapChartCanva'></canvas>
            </div>
        </div>
        <script src='https://cdn.jsdelivr.net/npm/chart.js'></script>
        <script src='/_assets/scripts/recapChart.js'></script>
        ";

        // Ajoute le graphique récapitulatif au corps de la page
        $this->body .= $graphique;
    }

    /**
     * Affiche le graphique du nombre de bâtiments par fichier.
     * @param array $dataArray Les données du nombre de bâtiments.
     * @param array $fileNames Les noms des fichiers.
     * @return void
     */
    public function afficherGraphiqueBatiments(): void {


        // Crée le HTML pour le graphique du nombre de bâtiments
        $graphique = "
        <div id='graphsBox'>
        <div class='graphiqueBox' id='zoneNbBatiments'>
            <h2>Nombre de bâtiments par fichier</h2>
            <div class='mainContentGraph'>
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
                    <canvas id='barBatiments'></canvas>
            </div>
        </div>
        <script src='https://cdn.jsdelivr.net/npm/chart.js'></script>
        <script src='/_assets/scripts/nombreBatiments.js'></script>
        ";

        // Ajoute le graphique au corps de la page
        $this->body .= $graphique;
    }

    /**
     * Affiche le graphique radar de l'aire moyenne par fichier.
     * @param array $dataArray Les données de l'aire moyenne.
     * @param array $fileNames Les noms des fichiers.
     * @return void
     */
    public function afficherGraphiqueRadarAireMoyenne(): void
    {
        $graphique = "
    <div class='graphiqueBox' id='zoneAireMoyenne'>
        <h2>Aire moyenne des batiments par fichier</h2>
        <div class='mainContentGraph'>
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
                <canvas id='radarAireMoyenne'></canvas>
        </div>
    </div>
    <script src='https://cdn.jsdelivr.net/npm/chart.js'></script>
    <script src='/_assets/scripts/aireMoyenneBatiments.js'></script>
    ";

        // Ajoute le graphique au corps de la page
        $this->body .= $graphique;
    }

    /**
     * Affiche le graphique de la distance moyenne entre bâtiments.
     * @param mixed $dataGeoJson Les données GeoJSON.
     * @param mixed $fileNamesGeojson Les noms des fichiers GeoJSON.
     * @return void
     */
    public function afficherGraphiqueDistanceMoyenne(): void
    {
        // Crée le HTML pour le graphique de la distance moyenne
        $graphique =  "

        <div class='graphiqueBox' id='zoneDistanceMoyenne'>
            <h2>Densité par fichier</h2>
            <div class='mainContentGraph'>
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



    /**
     * Affiche les visualisations Hillshade pour les fichiers TIFF.
     * @param array $dataArray Les données des fichiers TIFF.
     * @return void
     */
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

    /**
     * Affiche le formulaire de comparaison pour tester l'I.A.
     * @param array $dataArray Les données GeoJSON.
     * @param array $fileNames Les noms des fichiers GeoJSON.
     * @return void
     */
    public function afficheComparaisonTestIa($dataArray, $fileNames): void
    {
        $this->body .= '<form method="POST" action="/testIa">';
        $this->body .= '<div style="display: flex; flex-direction: column;">';

        foreach ($fileNames as $index => $fileName) {
            $checked = (isset($_POST['files']) && in_array((string)$index, $_POST['files'], true)) ? 'checked' : '';
            $this->body .= '<label>';
            $this->body .= '<input type="checkbox" name="files[]" value="' . htmlspecialchars($fileName) . '" ' . $checked . '> ' . htmlspecialchars($fileName);
            $this->body .= '</label>';
        }

        $this->body .= '</div>';
        $this->body .= '<button type="submit">Tester I.A</button>';
        $this->body .= '</form>';
    }

    /**
     * Affiche la page complète.
     * @return void
     */
    public function afficher(): void
    {
        parent::afficher();
    }
}