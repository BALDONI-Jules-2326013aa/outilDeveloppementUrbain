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

        foreach ($fileArray as $index => $file) {
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

            $airesMoyennes[$index] = $nombreDePolygones > 0 ? $airesTotales / $nombreDePolygones : 0;
        }

        print_r($airesMoyennes);
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



}


