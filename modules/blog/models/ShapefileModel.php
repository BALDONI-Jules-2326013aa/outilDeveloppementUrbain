<?php
namespace blog\models;

class ShapefileModel
{
    public static function convertToGeoJSON(array $shapeFiles): ?array
    {
        if (!isset($shapeFiles['shp']) || !isset($shapeFiles['shx']) || !isset($shapeFiles['dbf'])) {
            echo "Fichiers Shapefile incomplets : SHP, SHX, ou DBF manquant\n";
            return null;
        }

        // Détecter les autres fichiers utiles
        if (isset($shapeFiles['prj'])) echo "Fichier .prj détecté\n";
        if (isset($shapeFiles['cpg'])) echo "Fichier .cpg détecté\n";
        if (isset($shapeFiles['qpj'])) echo "Fichier .qpj détecté\n";
        if (isset($shapeFiles['sbn'])) echo "Fichier .sbn détecté\n";

        $shpFile = $shapeFiles['shp'];
        $geojson = ['type' => 'FeatureCollection', 'features' => []];

        // Ouvrir le fichier .shp
        $handle = fopen($shpFile, 'rb');
        if ($handle === false) {
            echo "Erreur d'ouverture du fichier .shp : $shpFile\n";
            return null;
        }

        // Lire l'en-tête du fichier (100 octets)
        $header = fread($handle, 100);
        if ($header === false) {
            echo "Erreur lors de la lecture de l'en-tête du fichier .shp\n";
            fclose($handle);
            return null;
        }

        // Journaliser l'en-tête du fichier
        echo "En-tête SHP : " . bin2hex($header) . "\n";

        // Lire les enregistrements
        while (!feof($handle)) {
            $recordHeader = fread($handle, 8);
            if ($recordHeader === false || strlen($recordHeader) < 8) {
                echo "Fin ou erreur lors de la lecture d'un enregistrement\n";
                break;
            }

            // Lire les 4 octets pour le type de géométrie
            $shapeTypeData = fread($handle, 4);
            if ($shapeTypeData === false || strlen($shapeTypeData) < 4) {
                echo "Erreur de lecture du type de géométrie\n";
                break;
            }

            $shapeType = unpack('V', $shapeTypeData)[1];
            echo "Type de géométrie détecté : $shapeType\n";

            // Traitement des différents types de géométrie
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

    // Fonction de parsing pour les points simples
    private static function parsePoint($handle): array
    {
        $pointData = fread($handle, 16); // 16 bytes for a point (X and Y, each 8 bytes)
        $point = unpack('dX/dY', $pointData); // Unpack the double precision numbers (64-bit)
        return [
            'type' => 'Feature',
            'geometry' => [
                'type' => 'Point',
                'coordinates' => [$point['X'], $point['Y']]
            ],
            'properties' => new \stdClass() // Empty properties
        ];
    }

    // Fonction de parsing pour les points avec valeur Z
    private static function parsePointZ($handle): array
    {
        $pointData = fread($handle, 24); // 24 bytes for a PointZ (X, Y, Z, each 8 bytes)
        $point = unpack('dX/dY/dZ', $pointData);
        return [
            'type' => 'Feature',
            'geometry' => [
                'type' => 'Point',
                'coordinates' => [$point['X'], $point['Y'], $point['Z']]
            ],
            'properties' => new \stdClass() // Empty properties
        ];
    }

    // Fonction de parsing pour les polylignes
    private static function parsePolyline($handle): array
    {
        // Lire l'enveloppe (bounding box)
        $bboxData = fread($handle, 32); // 32 bytes for bbox (4 doubles: Xmin, Ymin, Xmax, Ymax)
        $bbox = unpack('dXmin/dYmin/dXmax/dYmax', $bboxData);

        // Lire le nombre de parties et de points
        $numPartsAndPoints = fread($handle, 8); // 4 bytes for NumParts and 4 bytes for NumPoints
        $counts = unpack('VNumParts/VNumPoints', $numPartsAndPoints);
        $numParts = $counts['NumParts'];
        $numPoints = $counts['NumPoints'];

        // Lire l'index des parties
        $parts = [];
        if ($numParts > 0) {
            $partsData = fread($handle, 4 * $numParts); // 4 bytes for each part index
            $parts = unpack('V*', $partsData); // Unpack into an array of part indices
        }

        // Lire les points
        $points = [];
        for ($i = 0; $i < $numPoints; $i++) {
            $pointData = fread($handle, 16); // 16 bytes for each point (X and Y, each 8 bytes)
            $point = unpack('dX/dY', $pointData);
            $points[] = [$point['X'], $point['Y']];
        }

        return [
            'type' => 'Feature',
            'geometry' => [
                'type' => 'MultiLineString',
                'coordinates' => self::buildParts($parts, $points)
            ],
            'properties' => new \stdClass() // Empty properties
        ];
    }

    // Fonction de parsing pour les polylignes avec valeur Z
    private static function parsePolylineZ($handle): array
    {
        // Lire l'enveloppe (bounding box)
        $bboxData = fread($handle, 32); // 32 bytes for bbox (4 doubles: Xmin, Ymin, Xmax, Ymax)
        $bbox = unpack('dXmin/dYmin/dXmax/dYmax', $bboxData);

        // Lire le nombre de parties et de points
        $numPartsAndPoints = fread($handle, 8); // 4 bytes for NumParts and 4 bytes for NumPoints
        $counts = unpack('VNumParts/VNumPoints', $numPartsAndPoints);
        $numParts = $counts['NumParts'];
        $numPoints = $counts['NumPoints'];

        // Lire l'index des parties
        $parts = [];
        if ($numParts > 0) {
            $partsData = fread($handle, 4 * $numParts); // 4 bytes for each part index
            $parts = unpack('V*', $partsData); // Unpack into an array of part indices
        }

        // Lire les points avec les valeurs Z
        $points = [];
        for ($i = 0; $i < $numPoints; $i++) {
            $pointData = fread($handle, 24); // 24 bytes for each point (X, Y, Z, each 8 bytes)
            $point = unpack('dX/dY/dZ', $pointData);
            $points[] = [$point['X'], $point['Y'], $point['Z']];
        }

        return [
            'type' => 'Feature',
            'geometry' => [
                'type' => 'MultiLineString',
                'coordinates' => self::buildParts($parts, $points)
            ],
            'properties' => new \stdClass() // Empty properties
        ];
    }

    // Fonction de parsing pour les polygones simples
    private static function parsePolygon($handle): array
    {
        // Lire l'enveloppe (bounding box)
        $bboxData = fread($handle, 32); // 32 bytes for bbox (4 doubles: Xmin, Ymin, Xmax, Ymax)
        $bbox = unpack('dXmin/dYmin/dXmax/dYmax', $bboxData);

        // Lire le nombre de parties et de points
        $numPartsAndPoints = fread($handle, 8); // 4 bytes for NumParts and 4 bytes for NumPoints
        $counts = unpack('VNumParts/VNumPoints', $numPartsAndPoints);
        $numParts = $counts['NumParts'];
        $numPoints = $counts['NumPoints'];

        // Lire l'index des parties
        $parts = [];
        if ($numParts > 0) {
            $partsData = fread($handle, 4 * $numParts); // 4 bytes for each part index
            $parts = unpack('V*', $partsData); // Unpack into an array of part indices
        }

        // Lire les points
        $points = [];
        for ($i = 0; $i < $numPoints; $i++) {
            $pointData = fread($handle, 16); // 16 bytes for each point (X and Y, each 8 bytes)
            $point = unpack('dX/dY', $pointData);
            $points[] = [$point['X'], $point['Y']];
        }

        return [
            'type' => 'Feature',
            'geometry' => [
                'type' => 'Polygon',
                'coordinates' => self::buildParts($parts, $points)
            ],
            'properties' => new \stdClass() // Empty properties
        ];
    }

    // Fonction de parsing pour les polygones avec valeur Z
    private static function parsePolygonZ($handle): array
    {
        // Lire l'enveloppe (bounding box)
        $bboxData = fread($handle, 32); // 32 bytes for bbox (4 doubles: Xmin, Ymin, Xmax, Ymax)
        $bbox = unpack('dXmin/dYmin/dXmax/dYmax', $bboxData);

        // Lire le nombre de parties et de points
        $numPartsAndPoints = fread($handle, 8); // 4 bytes for NumParts and 4 bytes for NumPoints
        $counts = unpack('VNumParts/VNumPoints', $numPartsAndPoints);
        $numParts = $counts['NumParts'];
        $numPoints = $counts['NumPoints'];

        // Lire l'index des parties
        $parts = [];
        if ($numParts > 0) {
            $partsData = fread($handle, 4 * $numParts); // 4 bytes for each part index
            $parts = unpack('V*', $partsData); // Unpack into an array of part indices
        }

        // Lire les points avec les valeurs Z
        $points = [];
        for ($i = 0; $i < $numPoints; $i++) {
            $pointData = fread($handle, 24); // 24 bytes for each point (X, Y, Z, each 8 bytes)
            $point = unpack('dX/dY/dZ', $pointData);
            $points[] = [$point['X'], $point['Y'], $point['Z']];
        }

        return [
            'type' => 'Feature',
            'geometry' => [
                'type' => 'Polygon',
                'coordinates' => self::buildParts($parts, $points)
            ],
            'properties' => new \stdClass() // Empty properties
        ];
    }

    // Fonction utilitaire pour assembler les parties et points dans un format MultiLineString ou Polygon
    private static function buildParts(array $parts, array $points): array
    {
        $coordinates = [];
        $parts[] = count($points); // Ajouter la fin des points pour faciliter la boucle

        for ($i = 0; $i < count($parts) - 1; $i++) {
            $start = $parts[$i];
            $end = $parts[$i + 1];
            $coordinates[] = array_slice($points, $start, $end - $start);
        }

        return $coordinates;
    }

}

