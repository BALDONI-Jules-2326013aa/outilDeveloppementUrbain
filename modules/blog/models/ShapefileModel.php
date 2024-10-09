<?php

namespace blog\models;

class ShapefileModel
{
    public static function litSHP(): string
    {
        $shapefilePath = '/home/jules/Téléchargements/valenicina/2002/Building2002_ABM.shp';

        // Exécuter la commande ogrinfo pour lire le fichier Shapefile
        $output = shell_exec("ogrinfo -al -geom=geojson " . escapeshellarg($shapefilePath));

        // Trouver et extraire la partie GeoJSON (si présente)
        if (preg_match('/\{(?:[^{}]|(?R))*\}/', $output, $matches)) {
            return $matches[0];
        }

        return "Erreur : les données GeoJSON n'ont pas été trouvées.";
    }

}
