<?php

namespace blog\models;

class GeoJSONModel
{
    // Lit un fichier GeoJSON et renvoie les données sous forme de tableau associatif
    public static function litGeoJSON($file): array
    {
        $jsonData = file_get_contents($file);
        return json_decode($jsonData, true);
    }

    // Récupère l'année de la première feature d'un fichier GeoJSON
    public static function getGeoJSONYear($file): string
    {
        $content = file_get_contents($file);
        $data = json_decode($content, true);

        if (isset($data['features'][0]['properties']['Year'])) {
            return (string) $data['features'][0]['properties']['Year'];
        }

        return '';
    }

    // Calcule le nombre de bâtiments dans un tableau de fichiers GeoJSON
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

    // Calcule la surface totale des bâtiments dans un tableau de fichiers GeoJSON
    public static function recupereSurfaceTotale($fileArray): array
    {
        $listAireTotale = [];
        foreach ($fileArray as $file) {
            $totalArea = 0;
            if (isset($file['features'])) {
                foreach ($file['features'] as $feature) {
                    if (isset($feature['geometry']['type']) && in_array($feature['geometry']['type'], ['Polygon', 'MultiPolygon'])) {
                        $totalArea += self::calculatePolygonArea($feature['geometry']);
                    }
                }
            }
            $listAireTotale[] = $totalArea;
        }

        return $listAireTotale;
    }

    // Calcul simplifié de l'aire d'un polygone
    private static function calculatePolygonArea($geometry): float
    {
        // Implémentation simplifiée. Utilisation possible de bibliothèques comme Turf.js pour des calculs plus précis.
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

    // Génère le code HTML pour afficher le graphique en barres (nombre de bâtiments)
    public static function dessineGraphiqueNombreBatiments($nbBatimentsArray, $fileNameArray): string
    {
        $nbBatimentsJson = json_encode($nbBatimentsArray);
        $fileNamesJson = json_encode($fileNameArray);
        return "
    <div style='display: none;' id='nbBatimentsJson'>$nbBatimentsJson</div>
    <div style='display: none;' id='fileNamesJson'>$fileNamesJson</div>
    <canvas id='barBatiments' style='display: none'></canvas>
    <script src='https://cdn.jsdelivr.net/npm/chart.js'></script>
    <script src='/_assets/scripts/nombreBatiments.js'></script>
    ";
    }

    // Génère le code HTML pour afficher le graphique radar (surface totale des bâtiments)
    public static function dessineGraphiqueAireBatiments($aireBatimentsArray, $fileNameArray): string
    {
        $aireBatimentsJson = json_encode($aireBatimentsArray);
        $fileNamesJson = json_encode($fileNameArray);
        return "
    <div style='display: none;' id='aireBatimentsJson'>$aireBatimentsJson</div>
    <div style='display: none;' id='fileNamesJson'>$fileNamesJson</div>
    <canvas id='radarAireBatiments' style='display: none'></canvas>
    <script src='https://cdn.jsdelivr.net/npm/chart.js'></script>
    <script src='/_assets/scripts/aireBatiments.js'></script>
    ";
    }
}

?>
