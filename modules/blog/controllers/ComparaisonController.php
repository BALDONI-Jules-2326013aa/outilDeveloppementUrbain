<?php

namespace blog\controllers;
use blog\views\ComparaisonView;
class ComparaisonController
{
    public static function affichePage():void
    {
        session_start();
        $view = new ComparaisonView();
        $view->afficher();
    }
}