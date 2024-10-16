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
        $fileNames = []; // Pour stocker les noms de fichiers
        if (isset($_FILES['files']) && is_array($_FILES['files']['tmp_name'])) {
            foreach ($_FILES['files']['tmp_name'] as $key => $tmpName) {
                if (is_uploaded_file($tmpName)) {
                    $data = GeoJSONModel::litGeoJSON($tmpName);
                    if (!empty($data)) {
                        $dataArray[] = $data;
                        $fileNames[] = $_FILES['files']['name'][$key]; // Récupère le nom du fichier
                    }
                }
            }
        }

        // Gérer les nouveaux fichiers
        if (isset($_FILES['newFiles']) && is_array($_FILES['newFiles']['tmp_name'])) {
            foreach ($_FILES['newFiles']['tmp_name'] as $key => $tmpName) {
                if (is_uploaded_file($tmpName)) {
                    $data = GeoJSONModel::litGeoJSON($tmpName);
                    if (!empty($data)) {
                        $dataArray[] = $data;
                        $fileNames[] = $_FILES['newFiles']['name'][$key]; // Récupère le nom du nouveau fichier
                    }
                }
            }
        }

        $view = new ComparaisonView();
        $view->afficherCartes($dataArray, $fileNames); // Passe les noms de fichiers à la vue
        $view->afficherGraphiqueBatiments($dataArray, $fileNames); // Passe les noms de fichiers à la vue
        $view->afficher();
    }



    public static function affichePage(): void
    {
        session_start();
        $view = new ComparaisonView();
        $view->afficher();
    }
}
