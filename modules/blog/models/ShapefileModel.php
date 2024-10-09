<?php

namespace blog\models;

class ShapefileModel
{
    public static function litSHP(): string
    {
        $shapefilePath = '/home/jules/Téléchargements/valenicina/2002/Building2002_ABM.shp';

        // Exécuter la commande ogrinfo pour lire le fichier Shapefile
        $output = shell_exec("ogrinfo -al -geom=geojson " . escapeshellarg($shapefilePath));

        return $output ?: "Aucune donnée trouvée ou erreur lors de la lecture du fichier Shapefile.";
    }
}
