<?php

namespace blog\controllers;

use blog\views\HistoriqueCView;

class HistoriqueCController
{
    /**
     * Affiche la page de l'historique.
     * Démarre une session si elle n'est pas déjà démarrée et affiche la vue de l'historique.
     */
    public static function affichePage(): void
    {
        session_start();
        $view = new HistoriqueCView();
        $view->afficher();
    }
}