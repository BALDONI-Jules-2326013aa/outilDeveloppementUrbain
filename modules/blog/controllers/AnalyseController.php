<?php

namespace blog\controllers;

use blog\models\GeoJSONModel;
use blog\views\AnalyseView;
use blog\models\ShapefileModel;

class AnalyseController
{
    public static function affichePage(): void
    {
        session_start();

        // Appeler le modèle pour obtenir les données Shapefile
        $data = GeoJSONModel::litGeoJSON();

        // Créer la vue et afficher les données
        $view = new AnalyseView($data);
        $view->afficher();
    }
}
