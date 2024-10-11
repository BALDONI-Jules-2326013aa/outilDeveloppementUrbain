<?php

namespace blog\controllers;

use blog\models\GeoJSONModel;
use blog\views\AnalyseView;

class AnalyseController
{
    public static function affichePage(): void
    {
        session_start();

        $data = GeoJSONModel::litGeoJSON();

        $view = new AnalyseView($data);
        $view->afficher();
    }
}
