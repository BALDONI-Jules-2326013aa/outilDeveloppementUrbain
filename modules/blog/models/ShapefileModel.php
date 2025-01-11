<?php
namespace blog\models;

class ShapefileModel
{
    /**
     * Convertit un ensemble de fichiers Shapefile en GeoJSON.
     * @param array $shapeFiles Les fichiers Shapefile (SHP, SHX, DBF, etc.).
     * @return array|null Le GeoJSON converti ou null en cas d'erreur.
     */
    public static function convertToGeoJSON(array $shapeFiles): ?array
    {
        // Vérifie si les fichiers SHP, SHX et DBF sont présents
        if (!isset($shapeFiles['shp']) || !isset($shapeFiles['shx']) || !isset($shapeFiles['dbf'])) {
            echo "Fichiers Shapefile incomplets : SHP, SHX, ou DBF manquant\n";
            return null;
        }

        // Détecte et journalise les autres fichiers utiles
        if (isset($shapeFiles['prj'])) echo "Fichier .prj détecté\n";
        if (isset($shapeFiles['cpg'])) echo "Fichier .cpg détecté\n";
        if (isset($shapeFiles['qpj'])) echo "Fichier .qpj détecté\n";
        if (isset($shapeFiles['sbn'])) echo "Fichier .sbn détecté\n";

        $shpFile = $shapeFiles['shp'];
        $geojson = ['type' => 'FeatureCollection', 'features' => []];

        // Ouvre le fichier .shp
        $handle = fopen($shpFile, 'rb');
        if ($handle === false) {
            echo "Erreur d'ouverture du fichier .shp : $shpFile\n";
            return null;
        }

        // Lit l'en-tête du fichier (100 octets)
        $header = fread($handle, 100);
        if ($header === false) {
            echo "Erreur lors de la lecture de l'en-tête du fichier .shp\n";
            fclose($handle);
            return null;
        }

        // Journalise l'en-tête du fichier
        echo "En-tête SHP : " . bin2hex($header) . "\n";

        // Lit les enregistrements
        while (!feof($handle)) {
            $recordHeader = fread($handle, 8);
            if ($recordHeader === false || strlen($recordHeader) < 8) {
                echo "Fin ou erreur lors de la lecture d'un enregistrement\n";
                break;
            }

            // Lit les 4 octets pour le type de géométrie
            $shapeTypeData = fread($handle, 4);
            if ($shapeTypeData === false || strlen($shapeTypeData) < 4) {
                echo "Erreur de lecture du type de géométrie\n";
                break;
            }

            $shapeType = unpack('V', $shapeTypeData)[1];
            echo "Type de géométrie détecté : $shapeType\n";

            // Traite les différents types de géométrie
            switch ($shapeType) {
                case 1: // Point
                    $geojson['features'][] = self::parsePoint($handle);
                    break;
                case 3: // Polyline
                    $geojson['features'][] = self::parsePolyline($handle);
                    break;
                case 5: // Polygon
                    $geojson['features'][] = self::parsePolygon($handle);
                    break;
                case 15: // PolygonZ
                    $geojson['features'][] = self::parsePolygonZ($handle);
                    break;
                case 11: // PointZ
                    $geojson['features'][] = self::parsePointZ($handle);
                    break;
                case 13: // PolylineZ
                    $geojson['features'][] = self::parsePolylineZ($handle);
                    break;
                default:
                    echo "Type de forme non supporté : $shapeType\n";
                    break;
            }
        }

        fclose($handle);
        return $geojson;
    }

    /**
     * Fonction de parsing pour les points simples.
     * @param resource $handle Le handle du fichier SHP.
     * @return array Le point converti en GeoJSON.
     */
    private static function parsePoint($handle): array
    {
        $pointData = fread($handle, 16); // 16 octets pour un point (X et Y, chacun 8 octets)
        $point = unpack('dX/dY', $pointData); // Décode les nombres en double précision (64 bits)
        return [
            'type' => 'Feature',
            'geometry' => [
                'type' => 'Point',
                'coordinates' => [$point['X'], $point['Y']]
            ],
            'properties' => new \stdClass() // Propriétés vides
        ];
    }

    /**
     * Fonction de parsing pour les points avec valeur Z.
     * @param resource $handle Le handle du fichier SHP.
     * @return array Le point avec valeur Z converti en GeoJSON.
     */
    private static function parsePointZ($handle): array
    {
        $pointData = fread($handle, 24); // 24 octets pour un PointZ (X, Y, Z, chacun 8 octets)
        $point = unpack('dX/dY/dZ', $pointData);
        return [
            'type' => 'Feature',
            'geometry' => [
                'type' => 'Point',
                'coordinates' => [$point['X'], $point['Y'], $point['Z']]
            ],
            'properties' => new \stdClass() // Propriétés vides
        ];
    }

    /**
     * Fonction de parsing pour les polylignes.
     * @param resource $handle Le handle du fichier SHP.
     * @return array La polyligne convertie en GeoJSON.
     */
    private static function parsePolyline($handle): array
    {
        // Lit l'enveloppe (bounding box)
        $bboxData = fread($handle, 32); // 32 octets pour bbox (4 doubles : Xmin, Ymin, Xmax, Ymax)
        $bbox = unpack('dXmin/dYmin/dXmax/dYmax', $bboxData);

        // Lit le nombre de parties et de points
        $numPartsAndPoints = fread($handle, 8); // 4 octets pour NumParts et 4 octets pour NumPoints
        $counts = unpack('VNumParts/VNumPoints', $numPartsAndPoints);
        $numParts = $counts['NumParts'];
        $numPoints = $counts['NumPoints'];

        // Lit l'index des parties
        $parts = [];
        if ($numParts > 0) {
            $partsData = fread($handle, 4 * $numParts); // 4 octets pour chaque index de partie
            $parts = unpack('V*', $partsData); // Décode en un tableau d'index de parties
        }

        // Lit les points
        $points = [];
        for ($i = 0; $i < $numPoints; $i++) {
            $pointData = fread($handle, 16); // 16 octets pour chaque point (X et Y, chacun 8 octets)
            $point = unpack('dX/dY', $pointData);
            $points[] = [$point['X'], $point['Y']];
        }

        return [
            'type' => 'Feature',
            'geometry' => [
                'type' => 'MultiLineString',
                'coordinates' => self::buildParts($parts, $points)
            ],
            'properties' => new \stdClass() // Propriétés vides
        ];
    }

    /**
     * Fonction de parsing pour les polylignes avec valeur Z.
     * @param resource $handle Le handle du fichier SHP.
     * @return array La polyligne avec valeur Z convertie en GeoJSON.
     */
    private static function parsePolylineZ($handle): array
    {
        // Lit l'enveloppe (bounding box)
        $bboxData = fread($handle, 32); // 32 octets pour bbox (4 doubles : Xmin, Ymin, Xmax, Ymax)
        $bbox = unpack('dXmin/dYmin/dXmax/dYmax', $bboxData);

        // Lit le nombre de parties et de points
        $numPartsAndPoints = fread($handle, 8); // 4 octets pour NumParts et 4 octets pour NumPoints
        $counts = unpack('VNumParts/VNumPoints', $numPartsAndPoints);
        $numParts = $counts['NumParts'];
        $numPoints = $counts['NumPoints'];

        // Lit l'index des parties
        $parts = [];
        if ($numParts > 0) {
            $partsData = fread($handle, 4 * $numParts); // 4 octets pour chaque index de partie
            $parts = unpack('V*', $partsData); // Décode en un tableau d'index de parties
        }

        // Lit les points avec les valeurs Z
        $points = [];
        for ($i = 0; $i < $numPoints; $i++) {
            $pointData = fread($handle, 24); // 24 octets pour chaque point (X, Y, Z, chacun 8 octets)
            $point = unpack('dX/dY/dZ', $pointData);
            $points[] = [$point['X'], $point['Y'], $point['Z']];
        }

        return [
            'type' => 'Feature',
            'geometry' => [
                'type' => 'MultiLineString',
                'coordinates' => self::buildParts($parts, $points)
            ],
            'properties' => new \stdClass() // Propriétés vides
        ];
    }

    /**
     * Fonction de parsing pour les polygones simples.
     * @param resource $handle Le handle du fichier SHP.
     * @return array Le polygone converti en GeoJSON.
     */
    private static function parsePolygon($handle): array
    {
        // Lit l'enveloppe (bounding box)
        $bboxData = fread($handle, 32); // 32 octets pour bbox (4 doubles : Xmin, Ymin, Xmax, Ymax)
        $bbox = unpack('dXmin/dYmin/dXmax/dYmax', $bboxData);

        // Lit le nombre de parties et de points
        $numPartsAndPoints = fread($handle, 8); // 4 octets pour NumParts et 4 octets pour NumPoints
        $counts = unpack('VNumParts/VNumPoints', $numPartsAndPoints);
        $numParts = $counts['NumParts'];
        $numPoints = $counts['NumPoints'];

        // Lit l'index des parties
        $parts = [];
        if ($numParts > 0) {
            $partsData = fread($handle, 4 * $numParts); // 4 octets pour chaque index de partie
            $parts = unpack('V*', $partsData); // Décode en un tableau d'index de parties
        }

        // Lit les points
        $points = [];
        for ($i = 0; $i < $numPoints; $i++) {
            $pointData = fread($handle, 16); // 16 octets pour chaque point (X et Y, chacun 8 octets)
            $point = unpack('dX/dY', $pointData);
            $points[] = [$point['X'], $point['Y']];
        }

        return [
            'type' => 'Feature',
            'geometry' => [
                'type' => 'Polygon',
                'coordinates' => self::buildParts($parts, $points)
            ],
            'properties' => new \stdClass() // Propriétés vides
        ];
    }

    /**
     * Fonction de parsing pour les polygones avec valeur Z.
     * @param resource $handle Le handle du fichier SHP.
     * @return array Le polygone avec valeur Z converti en GeoJSON.
     */
    private static function parsePolygonZ($handle): array
    {
        // Lit l'enveloppe (bounding box)
        $bboxData = fread($handle, 32); // 32 octets pour bbox (4 doubles : Xmin, Ymin, Xmax, Ymax)
        $bbox = unpack('dXmin/dYmin/dXmax/dYmax', $bboxData);

        // Lit le nombre de parties et de points
        $numPartsAndPoints = fread($handle, 8); // 4 octets pour NumParts et 4 octets pour NumPoints
        $counts = unpack('VNumParts/VNumPoints', $numPartsAndPoints);
        $numParts = $counts['NumParts'];
        $numPoints = $counts['NumPoints'];

        // Lit l'index des parties
        $parts = [];
        if ($numParts > 0) {
            $partsData = fread($handle, 4 * $numParts); // 4 octets pour chaque index de partie
            $parts = unpack('V*', $partsData); // Décode en un tableau d'index de parties
        }

        // Lit les points avec les valeurs Z
        $points = [];
        for ($i = 0; $i < $numPoints; $i++) {
            $pointData = fread($handle, 24); // 24 octets pour chaque point (X, Y, Z, chacun 8 octets)
            $point = unpack('dX/dY/dZ', $pointData);
            $points[] = [$point['X'], $point['Y'], $point['Z']];
        }

        return [
            'type' => 'Feature',
            'geometry' => [
                'type' => 'Polygon',
                'coordinates' => self::buildParts($parts, $points)
            ],
            'properties' => new \stdClass() // Propriétés vides
        ];
    }

    /**
     * Fonction utilitaire pour assembler les parties et points dans un format MultiLineString ou Polygon.
     * @param array $parts Les index des parties.
     * @param array $points Les points.
     * @return array Les coordonnées assemblées.
     */
    private static function buildParts(array $parts, array $points): array
    {
        $coordinates = [];
        $parts[] = count($points); // Ajoute la fin des points pour faciliter la boucle

        for ($i = 0; $i < count($parts) - 1; $i++) {
            $start = $parts[$i];
            $end = $parts[$i + 1];
            $coordinates[] = array_slice($points, $start, $end - $start);
        }

        return $coordinates;
    }
}