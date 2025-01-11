<?php

namespace blog\models;
class GeoJSONModel
{
    // Lit le contenu d'un fichier GeoJSON et le retourne sous forme de tableau associatif
    public static function litGeoJSON($file): array
    {
        $jsonData = file_get_contents($file); // Lit le contenu du fichier
        return json_decode($jsonData, true); // Décode le JSON en tableau associatif
    }

    // Extrait l'année d'un fichier GeoJSON
    public static function getGeoJSONYear($file): string
    {
        $content = file_get_contents($file); // Lit le contenu du fichier
        $data = json_decode($content, true); // Décode le JSON en tableau associatif

        // Vérifie si l'année est présente dans les propriétés du premier élément
        if (isset($data['features'][0]['properties']['Year'])) {
            return (string)$data['features'][0]['properties']['Year']; // Retourne l'année en chaîne de caractères
        }

        return ''; // Retourne une chaîne vide si l'année n'est pas trouvée
    }

    // Renvoie le crs d'un fichier GeoJSON
    public static function getGeoJSONCRS($file): string
    {
        if (isset($file['crs']['properties']['name'])) {
            return $file['crs']['properties']['name']; // Retourne le crs si trouvé
        }
        else {
            return 'default'; // Retourne 'default' si le crs n'est pas trouvé
        }

    }

    // Récupère le nombre de bâtiments dans chaque fichier GeoJSON
    public static function recupereNombreBatiment($fileArray): array
    {
        $listNbBatiments = []; // Initialise un tableau pour stocker le nombre de bâtiments
        foreach ($fileArray as $file) {
            $buildingCount = 0; // Compteur de bâtiments
            if (isset($file['features'])) {
                foreach ($file['features'] as $feature) {
                    // Vérifie si le type de géométrie est un polygone ou un multipolygone
                    if (isset($feature['geometry']['type']) && in_array($feature['geometry']['type'], ['Polygon', 'MultiPolygon'])) {
                        $buildingCount++; // Incrémente le compteur de bâtiments
                    }
                }
            }
            $listNbBatiments[] = $buildingCount; // Ajoute le nombre de bâtiments au tableau
        }

        return $listNbBatiments; // Retourne le tableau des nombres de bâtiments
    }

    // Calcule la distance entre deux points géographiques en utilisant la formule de Haversine
    public static function formuleHaversine($lat1, $lon1, $lat2, $lon2): float
    {
        $R = 6371000; // Rayon de la Terre en mètres
        $phi1 = deg2rad($lat1); // Convertit la latitude du premier point en radians
        $phi2 = deg2rad($lat2); // Convertit la latitude du deuxième point en radians
        $deltaPhi = deg2rad($lat2 - $lat1); // Différence de latitude en radians
        $deltaLambda = deg2rad($lon2 - $lon1); // Différence de longitude en radians

        // Calcule la distance en utilisant la formule de Haversine
        $a = sin($deltaPhi / 2) ** 2 + cos($phi1) * cos($phi2) * sin($deltaLambda / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $R * $c; // Retourne la distance en mètres
    }

    // Calcule le point central d'un ensemble de coordonnées
    public static function calculPointCentral($coordinates): array
    {
        $sumLat = 0; // Somme des latitudes
        $sumLon = 0; // Somme des longitudes
        $count = count($coordinates); // Nombre de coordonnées

        // Calcule la somme des latitudes et des longitudes
        foreach ($coordinates as $coordinate) {
            $sumLon += $coordinate[0];
            $sumLat += $coordinate[1];
        }

        // Retourne le point central
        return [
            'lon' => $sumLon / $count,
            'lat' => $sumLat / $count,
        ];
    }

    // Récupère la distance moyenne entre les bâtiments dans chaque fichier GeoJSON
    public static function recupereDistanceMoyenneBatiments($fileArray): array
    {
        $moyennesParFichier = []; // Initialise un tableau pour stocker les distances moyennes par fichier

        foreach ($fileArray as $file) {
            $pointsCentraux = []; // Tableau pour stocker les points centraux des bâtiments
            $distances = []; // Tableau pour stocker les distances entre les points centraux

            if (isset($file['features'])) {
                foreach ($file['features'] as $feature) {
                    // Vérifie si le type de géométrie est un polygone
                    if (isset($feature['geometry']['type']) && $feature['geometry']['type'] === 'Polygon') {
                        $coordinates = $feature['geometry']['coordinates'][0]; // Récupère les coordonnées du polygone
                        $pointCentral = self::calculPointCentral($coordinates); // Calcule le point central du polygone
                        $pointsCentraux[] = $pointCentral; // Ajoute le point central au tableau
                    }
                }
            }

            // Calcule les distances entre tous les points centraux
            for ($i = 0; $i < count($pointsCentraux); $i++) {
                for ($j = $i + 1; $j < count($pointsCentraux); $j++) {
                    $distance = self::formuleHaversine(
                        $pointsCentraux[$i]['lat'],
                        $pointsCentraux[$i]['lon'],
                        $pointsCentraux[$j]['lat'],
                        $pointsCentraux[$j]['lon']
                    );
                    $distances[] = $distance; // Ajoute la distance au tableau
                }
            }

            // Calcule la distance moyenne
            $moyenne = count($distances) > 0 ? array_sum($distances) / count($distances) : 0;
            $moyennesParFichier[] = $moyenne; // Ajoute la distance moyenne au tableau
        }

        return $moyennesParFichier; // Retourne le tableau des distances moyennes par fichier
    }

    // Calcule l'aire moyenne, minimale et maximale des bâtiments dans chaque fichier GeoJSON
    public static function calculerAireMoyMinMax($fileArray): array
    {
        $resultats = [
            'aire_moyenne' => [],
            'aire_min_par_fichier' => [],
            'aire_max_par_fichier' => [],
            'aire_min_globale' => null,
            'aire_max_globale' => null,
        ];

        $globalAires = []; // Tableau pour stocker les aires de tous les fichiers

        foreach ($fileArray as $file) {
            $aires = []; // Tableau pour stocker les aires des bâtiments dans le fichier
            $nombreDePolygones = 0; // Compteur de polygones

            if (isset($file['features'])) {
                foreach ($file['features'] as $feature) {
                    // Vérifie si le type de géométrie est un polygone
                    if (isset($feature['geometry']['type']) && $feature['geometry']['type'] === 'Polygon') {
                        $coordinates = $feature['geometry']['coordinates'][0]; // Récupère les coordonnées du polygone
                        $aire = self::calculerAireBatiment($coordinates); // Calcule l'aire du polygone

                        $aires[] = $aire; // Ajoute l'aire au tableau
                        $nombreDePolygones++; // Incrémente le compteur de polygones
                    }
                }
            }

            // Calcule les aires moyenne, minimale et maximale pour le fichier
            if ($nombreDePolygones > 0) {
                $resultats['aire_moyenne'][] = array_sum($aires) / $nombreDePolygones;
                $resultats['aire_min_par_fichier'][] = min($aires);
                $resultats['aire_max_par_fichier'][] = max($aires);

                $globalAires = array_merge($globalAires, $aires); // Ajoute les aires au tableau global
            } else {
                $resultats['aire_moyenne'][] = 0;
                $resultats['aire_min_par_fichier'][] = 0;
                $resultats['aire_max_par_fichier'][] = 0;
            }
        }

        // Calcule les aires minimale et maximale globales
        if (!empty($globalAires)) {
            $resultats['aire_min_globale'] = min($globalAires);
            $resultats['aire_max_globale'] = max($globalAires);
        }

        return $resultats; // Retourne les résultats
    }

    // Calcule l'aire d'un bâtiment à partir de ses coordonnées
    public static function calculerAireBatiment(array $coordinates): float
    {
        $R = 6371000; // Rayon de la Terre en mètres
        $n = count($coordinates); // Nombre de coordonnées

        if ($n < 3) {
            return 0; // Retourne 0 si le nombre de coordonnées est inférieur à 3
        }

        $aire = 0.0; // Initialise l'aire

        // Calcule l'aire en utilisant la formule de l'aire sphérique
        for ($i = 0; $i < $n; $i++) {
            $lat1 = deg2rad($coordinates[$i][1]);
            $lon1 = deg2rad($coordinates[$i][0]);
            $lat2 = deg2rad($coordinates[($i + 1) % $n][1]);
            $lon2 = deg2rad($coordinates[($i + 1) % $n][0]);

            $aire += ($lon2 - $lon1) * (sin($lat1) + sin($lat2));
        }

        return abs($aire * $R * $R / 2); // Retourne l'aire absolue
    }

    public static function calculeTauxErreurDeuxFichiers($fileArray, $fileArray2): float|int
    {


        $nbBatiment1 = self::recupereNombreBatiment([$fileArray])[0];
        $nbBatiment2 = self::recupereNombreBatiment([$fileArray2])[0];
        $tauxNbBatiment = min($nbBatiment1, $nbBatiment2)/max($nbBatiment1, $nbBatiment2) * 100;

        $aireMoyenne1 = self::calculerAireMoyMinMax([$fileArray])[0];
        $aireMoyenne2 = self::calculerAireMoyMinMax([$fileArray2])[0];
        $tauxAireMoyenne = min($aireMoyenne1, $aireMoyenne2)/max($aireMoyenne1, $aireMoyenne2) * 100;
        /*
        $aireMax1 = self::calculerAireMoyMinMax([$fileArray])[1];
        $aireMax2 = self::calculerAireMoyMinMax([$fileArray2])[1];
        $tauxAireMax = max($aireMax2, $aireMax1)/min($aireMax2, $aireMax1) * 100;
    */
        $distanceMoyenne1 = self::recupereDistanceMoyenneBatiments([$fileArray])[0];
        $distanceMoyenne2 = self::recupereDistanceMoyenneBatiments([$fileArray2])[0];
        $tauxDistanceMoyenne = min($distanceMoyenne1, $distanceMoyenne2)/max($distanceMoyenne1, $distanceMoyenne2) * 100;

        return round( ($tauxDistanceMoyenne + $tauxAireMoyenne + $tauxNbBatiment) / 3, 2);

        //return 0;
    }

}

