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
            $listAireTotale[] = $totalArea * 1000000; // Multiplication pour avoir des valeurs lisibles
        }

        return $listAireTotale;
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
        return "
    <div style='display: none;' id='nbBatimentsJson'>$nbBatimentsJson</div>
    <div style='display: none;' id='fileNamesJson'>$fileNamesJson</div>
    <canvas id='barBatiments'></canvas>
    <script src='https://cdn.jsdelivr.net/npm/chart.js'></script>
    <script src='/_assets/scripts/nombreBatiments.js'></script>
    ";
    }

    public static function dessineGraphiqueRadarAireMoyenne($aireMoyenneArray, $fileNameArray): string
    {
        $aireMoyenneJson = json_encode($aireMoyenneArray);
        $fileNamesJson = json_encode($fileNameArray);
        return "
    <div style='display: none;' id='aireMoyenneJson'>$aireMoyenneJson</div>
    <div style='display: none;' id='fileNamesRadarJson'>$fileNamesJson</div>
    <canvas id='radarAireMoyenne'></canvas>
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
