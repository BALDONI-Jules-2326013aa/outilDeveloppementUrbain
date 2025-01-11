<?php

namespace blog\controllers;

use blog\models\GeoJSONModel;
use blog\views\SimulationView;

class SimulationController
{
    /**
     * Affiche la page de simulation.
     * @return void
     */
    public static function affichePage(): void
    {
        session_start();

        $view = new SimulationView();
        $view->afficher();
    }

    /**
     * Récupère les fichiers GeoJSON mis sur la page de simulation, les stocke dans la session et retourne un tableau contenant les données GeoJSON.
     * @return void
     */
    public static function afficheGetYears(): void
    {
        session_start(); // Démarre une nouvelle session ou reprend une session existante

        $filesNames = []; // Initialise un tableau vide pour stocker les noms des fichiers
        $filesYears = []; // Initialise un tableau vide pour stocker les années extraites des fichiers GeoJSON

        // Vérifie si des fichiers ont été téléchargés et si $_FILES['files']['tmp_name'] est un tableau
        if (isset($_FILES['files']) && is_array($_FILES['files']['tmp_name'])) {
            // Parcourt chaque fichier téléchargé
            foreach ($_FILES['files']['tmp_name'] as $key => $tmpName) {
                // Vérifie si le fichier a été téléchargé avec succès
                if (is_uploaded_file($tmpName)) {
                    // Extrait l'année du fichier GeoJSON en utilisant la méthode getGeoJSONYear de GeoJSONModel
                    $data = GeoJSONModel::getGeoJSONYear($tmpName);
                    $filesYears[] = $data; // Ajoute l'année extraite au tableau $filesYears
                    $filesNames[] = $_FILES['files']['name'][$key]; // Ajoute le nom du fichier au tableau $filesNames
                }
            }
        }

        $view = new SimulationView(); // Crée une nouvelle instance de SimulationView
        $view->afficherGetYears($filesYears, $filesNames); // Affiche les années et les noms des fichiers
        $view->afficher(); // Affiche la vue
    }

    /**
     * Démarre la simulation en utilisant des fichiers GeoJSON prédéfinis.
     * @return void
     */
    public static function startSimulation(): void
    {
        session_start();

        $geoData = [];
        $filesNames = [];

        // A MODIFIER QUAND ON AURA LE LOGICIEL

        // Pour l'instant, on fait comme si le logiciel renvoyait les fichiers /home/jules/Téléchargements/valenicina/donnes_projet/Household_3-2019.geojson et /home/jules/Téléchargements/valenicina/donnes_projet/Road_3-2019.geojson

        $geoData[] = GeoJSONModel::litGeoJSON("/home/jules/Téléchargements/valenicina/donnes_projet/Household_3-2019.geojson");
        $geoData[] = GeoJSONModel::litGeoJSON("/home/jules/Téléchargements/valenicina/donnes_projet/Road_3-2019.geojson");
        $filesNames[] = "Household_3-2019.geojson";
        $filesNames[] = "Road_3-2019.geojson";

        $view = new SimulationView();
        $view->resultatSimulation($geoData, $filesNames);
        $view->afficher();
    }
}