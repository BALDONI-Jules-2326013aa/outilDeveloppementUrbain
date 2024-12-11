<?php

namespace blog\controllers;
use blog\views\HistoriqueCView;
class HistoriqueCController
{
    public static function affichePage():void
    {
        session_start();
        $view = new HistoriqueCView();
        $view->afficher();
    }
}
