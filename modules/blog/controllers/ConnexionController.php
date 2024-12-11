<?php

namespace blog\controllers;

use blog\views\ConnexionView;
use blog\models\ConnexionModel;
use blog\models\DbConnect;

class ConnexionController
{
    public static function connecter(array $post): void
    {
        $courriel = htmlspecialchars($post["email"]);
        $password = htmlspecialchars($post["password"]);

        $connexionModel = new ConnexionModel(new DbConnect());

        if ($connexionModel->login($courriel, $password)) {
            setcookie(
                'courrielSiti',
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