<?php

namespace blog\controllers;
use blog\views\HistoriqueSView;
class HistoriqueSController
{
    public static function affichePage():void
    {
        session_start();
        $view = new HistoriqueSView();
        $view->afficher();
    }
}
