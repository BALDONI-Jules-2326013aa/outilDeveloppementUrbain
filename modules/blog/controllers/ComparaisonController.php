<?php

namespace blog\controllers;

use blog\models\GeoJSONModel;
use blog\views\ComparaisonView;

class ComparaisonController
{
    public static function afficheFichier(): void
    {
        session_start();

        $dataArray = [];
        $fileNames = [];
        if (isset($_FILES['files']) && is_array($_FILES['files']['tmp_name'])) {
            foreach ($_FILES['files']['tmp_name'] as $key => $tmpName) {
                if (is_uploaded_file($tmpName)) {
                    $data = GeoJSONModel::litGeoJSON($tmpName);
                    if (!empty($data)) {
                        $dataArray[] = $data;
                        $fileNames[] = $_FILES['files']['name'][$key];
                    }
                }
            }
        }

        $view = new ComparaisonView();
        $view->afficherAvecFichiers($dataArray, $fileNames);
    }


    public static function affichePage(): void
    {
        session_start();
        $view = new ComparaisonView();
        $view->afficher();
    }
}
