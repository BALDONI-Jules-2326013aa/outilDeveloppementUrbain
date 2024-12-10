<?php

namespace blog\controllers;

use blog\views\ConnexionView;

class ConnexionController
{
    public static function connecter(): void
    {
        session_start();
        $view = new ConnexionView();
        if ($view->verifierConnexion()) {
            $_SESSION['logged'] = true;
            header("Location: /");
            exit();
        } else {
            $view->afficher();
        }
    }
}