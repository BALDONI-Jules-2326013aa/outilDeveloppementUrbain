<?php

namespace blog\models;

class GeoJSONModel
{
    public static function litGeoJSON(): string
    {
        $geojsonFile = 'valenicina/2019/Buildings2019_ABM.geojson';
        $geojson = file_get_contents($geojsonFile);


        return $geojson;
    }
}

?>