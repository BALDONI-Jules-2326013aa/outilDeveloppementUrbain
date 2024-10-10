<?php

namespace blog\models;

class GeoJSONModel
{
    public static function litGeoJSON($file): string
    {
        echo file_get_contents($file);
        return file_get_contents($file);
    }
}

?>