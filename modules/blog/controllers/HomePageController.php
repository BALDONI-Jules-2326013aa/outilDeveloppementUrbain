<?php

namespace blog\controllers;
use blog\views\HomePageView;

class HomePageController
{
    public static function affichePage(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $view = new HomePageView();
        $view->afficher();
    }
}
