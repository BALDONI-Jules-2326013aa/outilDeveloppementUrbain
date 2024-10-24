<?php

namespace blog\controllers;

use blog\views\ConnexionView;
use blog\views\HomePageView;
use blog\views\inscriptionView;

class inscriptionController
{
    public static function affichePage():void
    {
        session_start();
        $view = new inscriptionView();
        $view->afficher();
    }

}