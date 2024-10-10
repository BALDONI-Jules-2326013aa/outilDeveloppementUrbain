<?php

namespace blog\models;

class GeoJSONModel
{
    public static function litGeoJSON($file): string
    {
        return file_get_contents($file);
    }
}

?>