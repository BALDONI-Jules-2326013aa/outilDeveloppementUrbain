<?php
namespace blog\models;

define('EARTH_RADIUS', 6371000); // Rayon de la Terre en mètres

class GeoJSONModel
{
    // Convertir des degrés en radians
    public static function toRadians($degree)
    {
        return $degree * M_PI / 180;
    }

    // Fonction pour lire un fichier GeoJSON et le décoder en tableau
    public static function litGeoJSON($file): array
    {

       // var_dump($file);
       // exit;
        if (file_exists($file)) {
            $jsonData = file_get_contents($file);
            $decodedData = json_decode($jsonData, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log('Error decoding JSON: ' . json_last_error_msg());
                echo'Error decoding JSON: ' . json_last_error_msg();
                exit();
                //return [];
            }

        }else{
            error_log("File not found: " . $file);
            echo "File not found: " . $file;
            // exit();


        }




       // return [ "toto" => "tata" ];
  // print_r($decodedData);
       return $decodedData ?? [];

    }


    // Calcul de l'aire d'un polygone
    public static function calculAires(array $coordinates): float
    {
        $totalArea = 0.0;
        $n = count($coordinates);

        if ($n < 3) {
            return 0.0;
        }

        // Affichage des coordonnées pour vérification
        foreach ($coordinates as $i => $point) {
            error_log("Point $i: Longitude = " . $point[0] . " | Latitude = " . $point[1]);
        }

        // Calcul de l'aire
        for ($i = 0; $i < $n; $i++) {
            $point1 = $coordinates[$i];
            $point2 = $coordinates[($i + 1) % $n];

            if (isset($point1[0], $point1[1], $point2[0], $point2[1])) {
                $lon1 = self::toRadians($point1[0]);
                $lat1 = self::toRadians($point1[1]);

                $lon2 = self::toRadians($point2[0]);
                $lat2 = self::toRadians($point2[1]);

                $totalArea += ($lon2 - $lon1) * (2 + sin($lat1) + sin($lat2));

            }
        }

        $totalArea = abs($totalArea * EARTH_RADIUS * EARTH_RADIUS / 2);

        return $totalArea;

    }

    public static function Test($file): array
    {
        return [ "toto" => "tata" ];
    }

    public static function TrouverMinMaxMoy(string $file): array
    {
        //print_r(self::Test($file));
        print_r(self::litGeoJSON($file));

        $data = self::litGeoJSON($file);

        if (empty($data)) {
            error_log("Le fichier GeoJSON est vide ou n'a pas pu être lu.");
            return [
                'min_area' => 0,
                'max_area' => 0,
                'avg_area' => 0,
                'all_areas' => []
            ];
        }

        $areas = [];

        foreach ($data['features'] as $feature) {
            if (isset($feature['geometry']['type']) && in_array($feature['geometry']['type'], ['Polygon', 'MultiPolygon'])) {
                if ($feature['geometry']['type'] === 'Polygon' && isset($feature['geometry']['coordinates'][0])) {
                    $coordinates = $feature['geometry']['coordinates'][0];
                    $area = self::calculAires($coordinates);
                    if ($area > 0) {
                        $areas[] = $area;
                    } else {
                        error_log("Aire calculée égale à 0 pour un polygone.");
                    }
                } elseif ($feature['geometry']['type'] === 'MultiPolygon') {
                    foreach ($feature['geometry']['coordinates'] as $polygon) {
                        if (isset($polygon[0])) {
                            $coordinates = $polygon[0];
                            $area = self::calculAires($coordinates);
                            if ($area > 0) {
                                $areas[] = $area;
                            } else {
                                error_log("Aire calculée égale à 0 pour un multipolygone.");
                            }
                        }
                    }
                }
            } else {
                error_log("Type de géométrie non pris en charge ou coordonnées manquantes.");
            }
        }

        if (empty($areas)) {
            error_log("Aucune aire valide n'a été calculée.");
            return [
                'min_area' => 0,
                'max_area' => 0,
                'avg_area' => 0,
                'all_areas' => []
            ];
        }

        $minArea = min($areas);
        $maxArea = max($areas);
        $avgArea = array_sum($areas) / count($areas);

        return [
            'min_area' => $minArea,
            'max_area' => $maxArea,
            'avg_area' => $avgArea,
            'all_areas' => $areas
        ];
    }

    // Générer le HTML et le JavaScript pour afficher un graphique des aires
    public static function choreographer($AreaArray, $filenameArray): string
    {
        $Area = json_encode($AreaArray);
        $filename = json_encode($filenameArray);
        return "
        <div style='display: none;' id='aireJson'>$Area</div>
        <div style='display: none;' id='fileNamesJson'>$filename</div>
        <canvas id='spiderDiagram' style='width: 150px; height: 150px;'></canvas>
        <script src='https://cdn.jsdelivr.net/npm/chart.js'></script>
        <script src='/_assets/scripts/spiderDiagram.js'></script>";
    }
}
?>