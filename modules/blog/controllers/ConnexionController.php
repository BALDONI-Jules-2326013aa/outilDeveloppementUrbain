<?php

namespace blog\controllers;

use blog\views\ConnexionView;
use blog\models\ConnexionModel;
use blog\models\DbConnect;

class ConnexionController
{
    public static function affichePage(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $view = new ConnexionView();
        $view->afficher();
    }

    public static function connecter(array $post): void
    {
        $courriel = htmlspecialchars($post["username"]);
        $password = htmlspecialchars($post["password"]);

        $dbConnect = new DbConnect();
        $db = $dbConnect->connect();

        if ($dbConnect->isConnected()) {
            $connexionModel = new ConnexionModel($db);

            if ($connexionModel->verifConnexion($courriel, $password)) {
                setcookie(
                    'courrielSiti',
                    $post["username"],
                    [
                        'expires' => time() + 365*24*3600,
                        'secure' => true,
                        'httponly' => true,
                    ]
                );
                header("Location: /");
                exit();
            } else {
                echo "Mail ou mot de passe incorrect";
                exit();
            }
        } else {
            echo "Erreur de connexion à la base de données.";
            exit();
        }
    }
}