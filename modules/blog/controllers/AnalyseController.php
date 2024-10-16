<?php

namespace blog\controllers;

use blog\models\GeoJSONModel;
use blog\views\AnalyseView;

class AnalyseController
{
    public static function affichePage(): void
    {
        session_start();


        $view = new AnalyseView();
        $view->afficher();
    }

    public static function afficheSimulation(): void
    {
        session_start();

        $filesNames = [];
        $filesYears = [];

        if (isset($_FILES['files']) && is_array($_FILES['files']['tmp_name'])) {
            foreach ($_FILES['files']['tmp_name'] as $key => $tmpName) {
                if (is_uploaded_file($tmpName)) {
                    $data = GeoJSONModel::getGeoJSONYear($tmpName);
                    if (!empty($data)) {
                        $filesYears[] = $data;
                        $filesNames[] = $_FILES['files']['tmp_name'][$key];
                    }
                }
            }
        }

        $view = new AnalyseView();
        $view->afficherSimulation($filesYears, $filesNames);
        $view->afficher();
    }
}
