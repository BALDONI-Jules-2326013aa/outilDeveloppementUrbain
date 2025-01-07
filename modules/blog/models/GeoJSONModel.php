<?php

namespace blog\models;
class GeoJSONModel
{
    public static function litGeoJSON($file): array
    {
        $jsonData = file_get_contents($file);
        return json_decode($jsonData, true);
    }

    public static function getGeoJSONYear($file): string
    {
        $content = file_get_contents($file);
        $data = json_decode($content, true);

        if (isset($data['features'][0]['properties']['Year'])) {
            return (string)$data['features'][0]['properties']['Year'];
        }

        return '';
    }

    public static function recupereNombreBatiment($fileArray): array
    {
        $listNbBatiments = [];
        foreach ($fileArray as $file) {
            $buildingCount = 0;
            if (isset($file['features'])) {
                foreach ($file['features'] as $feature) {
                    if (isset($feature['geometry']['type']) && in_array($feature['geometry']['type'], ['Polygon', 'MultiPolygon'])) {
                        $buildingCount++;
                    }
                }
            }
            $listNbBatiments[] = $buildingCount;
        }

        return $listNbBatiments;
    }

    public static function formuleHaversine($lat1, $lon1, $lat2, $lon2): float
    {
        $R = 6371000; // Rayon de la Terre en kilomètres
        $phi1 = deg2rad($lat1);
        $phi2 = deg2rad($lat2);
        $deltaPhi = deg2rad($lat2 - $lat1);
        $deltaLambda = deg2rad($lon2 - $lon1);

        $a = sin($deltaPhi / 2) ** 2 + cos($phi1) * cos($phi2) * sin($deltaLambda / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $R * $c;
    }

    public static function calculPointCentral($coordinates): array
    {
        $sumLat = 0;
        $sumLon = 0;
        $count = count($coordinates);

        foreach ($coordinates as $coordinate) {
            $sumLon += $coordinate[0];
            $sumLat += $coordinate[1];
        }

        return [
            'lon' => $sumLon / $count,
            'lat' => $sumLat / $count,
        ];
    }

    public static function recupereDistanceMoyenneBatiments($fileArray): float
    {
        $pointsCentraux = [];
        foreach ($fileArray as $file) {
            if (isset($file['features'])) {
                foreach ($file['features'] as $feature) {
                    if (isset($feature['geometry']['type']) && $feature['geometry']['type'] === 'Polygon') {
                        $coordinates = $feature['geometry']['coordinates'][0];
                        $pointCentral = self::calculPointCentral($coordinates);
                        $pointsCentraux[] = $pointCentral;
                    }
                }
            }
        }

        $totalDistance = 0;
        $comparisons = 0;

        for ($i = 0; $i < count($pointsCentraux); $i++) {
            for ($j = $i + 1; $j < count($pointsCentraux); $j++) {
                $distance = self::formuleHaversine(
                    $pointsCentraux[$i]['lat'],
                    $pointsCentraux[$i]['lon'],
                    $pointsCentraux[$j]['lat'],
                    $pointsCentraux[$j]['lon']
                );
                $totalDistance += $distance;
                $comparisons++;
            }
        }

        return ($comparisons > 0 ? $totalDistance / $comparisons : 0);
    }

    public function dessineGraphiqueDistanceMoyenne(int $distanceMoyenne, mixed $fileNamesGeojson): string
    {
        // todo
        // Faire l'affichage visuel des donnees
        return ;
    }

    private static function calculatePolygonArea($geometry): float
    {
        $area = 0;

        if ($geometry['type'] === 'Polygon') {
            foreach ($geometry['coordinates'][0] as $i => $coord) {
                if ($i < count($geometry['coordinates'][0]) - 1) {
                    $x1 = $coord[0];
                    $y1 = $coord[1];
                    $x2 = $geometry['coordinates'][0][$i + 1][0];
                    $y2 = $geometry['coordinates'][0][$i + 1][1];
                    $area += $x1 * $y2 - $x2 * $y1;
                }
            }
            $area = abs($area / 2);
        }

        return $area;
    }

    public static function recupereSurfaceMoyenneBatiments($fileArray): array
    {
        $listAireMoyenne = [];
        foreach ($fileArray as $file) {
            $totalArea = 0;
            $buildingCount = 0;
            if (isset($file['features'])) {
                foreach ($file['features'] as $feature) {
                    if (isset($feature['geometry']['type']) && in_array($feature['geometry']['type'], ['Polygon', 'MultiPolygon'])) {
                        $totalArea += self::calculatePolygonArea($feature['geometry']);
                        $buildingCount++;
                    }
                }
            }
            $moyenne = $buildingCount > 0 ? $totalArea / $buildingCount : 0;
            $listAireMoyenne[] = $moyenne;
        }

        return $listAireMoyenne;
    }

    public static function recupereTypeBatiment($fileArray): array
    {
        $buildingTypes = [];
        foreach ($fileArray as $file) {
            if (isset($file['features'])) {
                foreach ($file['features'] as $feature) {
                    $type = $feature['properties']['ID_2'] ?? null;
                    if ($type) {
                        if ($type == 'New'){
                            $type = 'House';
                        }
                        if ($type == 'house'){
                            $type = 'House';
                        }
                        if (!isset($buildingTypes[$type])) {
                            $buildingTypes[$type] = 0;
                        }
                        $buildingTypes[$type]++;
                    }
                }
            }
        }
        return $buildingTypes;
    }

    public static function dessineGraphiqueNombreBatiments($nbBatimentsArray, $fileNameArray): string
    {
        $nbBatimentsJson = json_encode($nbBatimentsArray);
        $fileNamesJson = json_encode($fileNameArray);

        $colorPickersHtml = '';
        foreach ($fileNameArray as $index => $fileName) {
            $colorPickersHtml .= "
        <div class='color-picker'>
            <label for='color_$index'>Couleur pour $fileName :</label>
            <input type='color' id='colorNbBatiments_$index' class='color-input' value='#" . substr(md5($fileName), 0, 6) . "'>
        </div>";
        }

        return "
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
                    <h4>Choisir les couleurs :</h4>
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
    }

    public static function dessineGraphiqueRadarAireMoyenne($aireMoyenneArray, $fileNameArray): string
    {
        $aireMoyenneJson = json_encode($aireMoyenneArray);
        $fileNamesJson = json_encode($fileNameArray);

        $colorPickersHtml = '';
        foreach ($fileNameArray as $index => $fileName) {
            $colorPickersHtml .= "
        <div class='color-picker'>
            <label for='colorAireMoyenne_$index'>Couleur pour $fileName :</label>
            <input type='color' id='colorAireMoyenne_$index' class='color-input' value='#" . substr(md5($fileName), 0, 6) . "'>
        </div>";
        }

        return "
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
                    <h4>Choisir les couleurs :</h4>
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
    }

    public static function dessineGraphiquePolarTypeBat($typeBatimentMap, $fileNameArray): string
    {
        $typeBatimentMapJson = json_encode($typeBatimentMap);
        $fileNamesJson = json_encode($fileNameArray);
        return "
        <div style='display: none;' id='typeBatimentMapJson'>$typeBatimentMapJson</div>
        <div style='display: none;' id='fileNamesPolarJson'>$fileNamesJson</div>
        <canvas id='polarTypeBatiment'></canvas>
        <script src='https://cdn.jsdelivr.net/npm/chart.js'></script>
        <script src='/_assets/scripts/TypeBat.js'></script>
        ";
    }


}


