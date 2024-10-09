<?php

namespace blog\controllers;
use blog\views\AnalyseView;
class AnalyseController
{
    public static function affichePage():void
    {
        session_start();
        $view = new AnalyseView();
        $view->afficher();
    }
}