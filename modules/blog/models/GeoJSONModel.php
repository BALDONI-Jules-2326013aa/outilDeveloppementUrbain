<?php

namespace blog\models;

class GeoJSONModel
{
    public static function litGeoJSON($file): array
    {
        $jsonData = file_get_contents($file);
        return json_decode($jsonData, true);
    }

    public static function recupereNombreBaptiment($fileArray): array
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

    public static function dessineGraphique($nbBatimentsArray, $fileNameArray): string
    {
        $nbBatimentsJson = json_encode($nbBatimentsArray);
        $fileNamesJson = json_encode($fileNameArray);
        return "
    <div style='display: none;' id='nbBatimentsJson'>$nbBatimentsJson</div>
    <div style='display: none;' id='fileNamesJson'>$fileNamesJson</div>
    <canvas id='barBatiments'></canvas>
    <script src='https://cdn.jsdelivr.net/npm/chart.js'></script>
    <script src='/_assets/scripts/nombreBatiments.js'></script>
    ";
    }




}

?>
