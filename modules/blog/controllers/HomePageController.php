<?php

namespace blog\views;
use HomePageView;
class HomePageController
{
    public static function affichePage():void
    {
        session_start();
        $view = new HomePageView();
        $view->afficher();
    }
}
