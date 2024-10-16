<?php

namespace blog\models;

class ShapefileModel
{
    public static function litSHP(): string
    {
        $shapefilePath = '/home/jules/Téléchargements/valenicina/2002/Building2002_ABM.shp';

        $output = shell_exec("ogrinfo -al -geom=geojson " . escapeshellarg($shapefilePath));

        if (preg_match('/\{(?:[^{}]|(?R))*\}/', $output, $matches)) {
            return $matches[0];
        }

        return "Erreur : les données GeoJSON n'ont pas été trouvées.";
    }

}

?>
