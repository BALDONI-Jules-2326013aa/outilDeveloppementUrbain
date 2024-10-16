<?php

namespace blog\controllers;

use blog\models\GeoJSONModel;
use blog\views\SimulationView;

class SimulationController
{
    public static function affichePage(): void
    {
        session_start();


        $view = new SimulationView();
        $view->afficher();
    }

    public static function afficheGetYears(): void
    {
        session_start();

        $filesNames = [];
        $filesYears = [];

        if (isset($_FILES['files']) && is_array($_FILES['files']['tmp_name'])) {
            foreach ($_FILES['files']['tmp_name'] as $key => $tmpName) {
                if (is_uploaded_file($tmpName)) {
                    $data = GeoJSONModel::getGeoJSONYear($tmpName);
                    $filesYears[] = $data;
                    $filesNames[] = $_FILES['files']['name'][$key];
                }
            }
        }

        $view = new SimulationView();
        $view->afficherGetYears($filesYears, $filesNames);
        $view->afficher();
    }

    public static function startSimulation(): void
    {
        session_start();

        $geoData = [];
        $filesNames = [];

        // A MODIFIER QUAND ON AURA LE LOGICIEL

        // Pour l'instant, on fait comme si le logiciel renvoyait les fichiers _assets/testSimul/Household_3-2019.geojson et _assets/testSimul/Road_3-2019.geojson

        $geoData[] = GeoJSONModel::litGeoJSON("_assets/testSimul/Household_3-2019.geojson");
        $geoData[] = GeoJSONModel::litGeoJSON("_assets/testSimul/Road_3-2019.geojson");
        $filesNames[] = "Household_3-2019.geojson";
        $filesNames[] = "Road_3-2019.geojson";


        $view = new SimulationView();
        $view->resultatSimulation($geoData, $filesNames);
        $view->afficher();
    }

    public static function downloadSimulationFiles(): void
    {
        session_start();

        // A CHANGER QUAND ON AURA LE LOGICIEL
        $filePaths = [
            "_assets/testSimul/Household_3-2019.geojson",
            "_assets/testSimul/Road_3-2019.geojson",
        ];

        $zip = new \ZipArchive();
        $zipFileName = '/tmp/fichiers_simules.zip';

        if ($zip->open($zipFileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            foreach ($filePaths as $file) {
                if (file_exists($file)) {
                    $zip->addFile($file, basename($file));
                }
            }
            $zip->close();

            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="fichiers_simules.zip"');
            header('Content-Length: ' . filesize($zipFileName));
            readfile($zipFileName);

            unlink($zipFileName);
            exit;
        } else {
            echo "Erreur lors de la création du fichier ZIP.";
        }
    }

}
