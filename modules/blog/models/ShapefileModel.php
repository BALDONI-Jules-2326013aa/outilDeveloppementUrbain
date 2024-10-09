<?php

namespace blog\models;

class ShapefileModel
{
    public static function litSHP($shapefilePath): string
    {
        // Exécuter la commande ogrinfo pour lire le fichier Shapefile
        $output = shell_exec("ogrinfo -al -geom=geojson " . escapeshellarg($shapefilePath));

        return $output ?: "Aucune donnée trouvée ou erreur lors de la lecture du fichier Shapefile.";
    }
}
