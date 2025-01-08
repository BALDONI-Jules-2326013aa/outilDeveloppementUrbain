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

    public static function recupereDistanceMoyenneBatiments($fileArray): array
    {
        $moyennesParFichier = [];

        foreach ($fileArray as $file) {
            $pointsCentraux = [];
            $distances = [];

            if (isset($file['features'])) {
                foreach ($file['features'] as $feature) {
                    if (isset($feature['geometry']['type']) && $feature['geometry']['type'] === 'Polygon') {
                        $coordinates = $feature['geometry']['coordinates'][0];
                        $pointCentral = self::calculPointCentral($coordinates);
                        $pointsCentraux[] = $pointCentral;
                    }
                }
            }

            for ($i = 0; $i < count($pointsCentraux); $i++) {
                for ($j = $i + 1; $j < count($pointsCentraux); $j++) {
                    $distance = self::formuleHaversine(
                        $pointsCentraux[$i]['lat'],
                        $pointsCentraux[$i]['lon'],
                        $pointsCentraux[$j]['lat'],
                        $pointsCentraux[$j]['lon']
                    );
                    $distances[] = $distance;
                }
            }

            $moyenne = count($distances) > 0 ? array_sum($distances) / count($distances) : 0;
            $moyennesParFichier[] = $moyenne;
        }

        return $moyennesParFichier;
    }

    public static function calculerAireMoyMinMax($fileArray): array
    {
        $airesMoyennes = [];

        foreach ($fileArray as $file) {
            $airesTotales = 0;
            $nombreDePolygones = 0;

            if (isset($file['features'])) {
                foreach ($file['features'] as $feature) {
                    if (isset($feature['geometry']['type']) && $feature['geometry']['type'] === 'Polygon') {
                        $coordinates = $feature['geometry']['coordinates'][0];
                        $aire = self::calculerAireBatiment($coordinates);

                        $airesTotales += $aire;
                        $nombreDePolygones++;
                    }
                }
            }

            $airesMoyennes[$file['name']] = $nombreDePolygones > 0 ? $airesTotales / $nombreDePolygones : 0;
        }

        return $airesMoyennes;
    }


    public static function calculerAireBatiment(array $coordinates): float
    {
        $R = 6371000;
        $n = count($coordinates);

        if ($n < 3) {
            return 0;
        }

        $aire = 0.0;

        for ($i = 0; $i < $n; $i++) {
            $lat1 = deg2rad($coordinates[$i][1]);
            $lon1 = deg2rad($coordinates[$i][0]);
            $lat2 = deg2rad($coordinates[($i + 1) % $n][1]);
            $lon2 = deg2rad($coordinates[($i + 1) % $n][0]);

            $aire += ($lon2 - $lon1) * (sin($lat1) + sin($lat2));
        }

        return abs($aire * $R * $R / 2);
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

    public static function dessineGraphiqueDistanceMoyenne($distanceMoyenneArray, $fileNameArray): string
    {
        $distanceMoyenneJson = json_encode($distanceMoyenneArray);
        $fileNamesJson = json_encode($fileNameArray);

        $colorPickersHtml = '';
        foreach ($fileNameArray as $index => $fileName) {
            $colorPickersHtml .= "
        <div class='color-picker'>
            <label for='colorDistanceMoyenne_$index'>Couleur pour $fileName :</label>
            <input type='color' id='colorDistanceMoyenne_$index' class='color-input' value='#" . substr(md5($fileName), 0, 6) . "'>
        </div>";
        }

        return "
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


