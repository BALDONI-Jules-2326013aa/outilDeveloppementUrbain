<?php

namespace blog\controllers;
use views\Fragments\HomePageView;
class HomePageController
{
    public static function affichePage():void
    {
        session_start();
        $view = new HomePageView();
        $view->afficher();
    }
}
