<?php

namespace blog\controllers;
use blog\models\ShapefileModel;
use blog\views\AnalyseView;
use blog\views\ComparaisonView;

class ComparaisonController
{
    public static function recupFichier()
    {

    }

    public static function afficheFichier(): void
    {
        session_start();

        // Appeler le modèle pour obtenir les données Shapefile
        $data = ShapefileModel::litSHP($_POST['file1']);

        // Créer la vue et afficher les données
        $view = new ComparaisonView();
        $view->afficherAvecFichier($data);
    }

    public static function affichePage():void
    {
        session_start();
        $view = new ComparaisonView();
        $view->afficher();
    }
}