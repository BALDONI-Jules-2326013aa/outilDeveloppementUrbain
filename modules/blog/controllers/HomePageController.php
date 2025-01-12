<?php

namespace blog\controllers;
use blog\views\HomePageView;

class HomePageController
{
    /**
     * Affiche la page d'accueil.
     * Si la session n'est pas démarrée, la démarre.
     */
    public static function affichePage(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $view = new HomePageView();
        $view->afficher();
    }
}