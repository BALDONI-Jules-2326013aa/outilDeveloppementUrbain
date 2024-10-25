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
}
