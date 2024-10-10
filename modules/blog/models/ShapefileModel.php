<?php

namespace blog\models;

class ShapefileModel
{
    public static function litSHP($shapefilePath): string
    {
        // Exécuter la commande ogrinfo pour lire le fichier Shapefile
        $output = shell_exec("ogrinfo -al -geom=geojson " . escapeshellarg($shapefilePath));

        if (preg_match('/\{(?:[^{}]|(?R))*\}/', $output, $matches)) {
            return $matches[0];
        }

        return "Erreur : les données GeoJSON n'ont pas été trouvées.";
    }

}

?>
