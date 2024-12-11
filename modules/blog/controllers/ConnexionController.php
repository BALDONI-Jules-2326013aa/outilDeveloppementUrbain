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
        $username = htmlspecialchars($post["username"] ?? '');
        $password = htmlspecialchars($post["password"] ?? '');

        $dbConnect = new DbConnect();
        $db = $dbConnect->connect();

        if ($dbConnect->isConnected()) {
            $connexionModel = new ConnexionModel($db);

            if ($connexionModel->verifConnexion($username, $password)) {
                setcookie(
                    'courrielSiti',
                    $username,
                    [
                        'expires' => time() + 365*24*3600,
                        'secure' => true,
                        'httponly' => true,
                    ]
                );
                header("Location: /");
                exit();
            } else {
                echo "Nom d'utilisateur ou mot de passe incorrect";
                exit();
            }
        } else {
            echo "Erreur de connexion à la base de données.";
            exit();
        }
    }

    public static function deconnecter(): void
    {
        $connexionModel = new ConnexionModel(new DbConnect());
        $connexionModel->logout();

        header("Location: /connexion");
        exit();
    }
}