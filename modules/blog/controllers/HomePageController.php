<?php

namespace blog\controllers;
use blog\views\HomePageView;

class HomePageController
{
    public static function affichePage():void
    {
        session_start();
        $view = new HomePageView();
        $view->afficher();
    }
}
