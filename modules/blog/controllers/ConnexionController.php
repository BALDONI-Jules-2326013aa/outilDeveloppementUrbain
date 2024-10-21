<?php

namespace blog\controllers;

use blog\views\ConnexionView;
use blog\views\HomePageView;

class ConnexionController
{
    public static function affichePage():void
    {
        session_start();
        $view = new ConnexionView();
        $view->afficher();
    }

}