<?php

namespace blog\controllers;

use blog\models\ConnexionModel;
use blog\views\ConnexionView;

class ConnexionController
{
    public static function affichePage(): void
    {
        session_start();
        $view = new ConnexionView();
        $view->afficher();
    }

    public static function connecter(array $post): void
    {
        $courriel = htmlspecialchars($post["email"]);
        $password = htmlspecialchars($post["password"]);

        $connexionModel = new ConnexionModel(new DbConnect());

        if ($connexionModel->login($courriel, $password)) {
            setcookie(
                'corrielSiti',
                $post["email"],
                [
                    'expires' => time() + 365*24*3600,
                    'secure' => true,
                    'httponly' => true,
                ]
            );
            header("Location: /home");
            exit();
        } else {
            echo "Mail ou mot de passe incorrect";
            exit();
        }
    }


}
