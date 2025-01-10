<?php

namespace blog\controllers;

use blog\views\ConnexionView;
use blog\models\ConnexionModel;
use blog\models\DbConnect;

class ConnexionController
{
    public static function affichePage(): void
    {
        // Démarrage de la session si elle n'est pas déjà démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $view = new ConnexionView();
        $view->afficher();
    }

    // fonction de connexion
    public static function connecter(array $post): void
    {
        $username = htmlspecialchars($post["username"] ?? '');
        $password = htmlspecialchars($post["password"] ?? '');

        // Vérification des champs
        $dbConnect = new DbConnect();
        $db = $dbConnect->connect();

        // Si la connexion à la base de données est établie
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
                //redirection vers la page d'accueil
                header("Location: /");
                exit();
            } else {
                // Si l'utilisateur n'est pas trouvé dans la base de données
                echo "Nom d'utilisateur ou mot de passe incorrect";
                exit();
            }
        } else {
            // Si la connexion à la base de données n'est pas établie
            echo "Erreur de connexion à la base de données.";
            exit();
        }
    }

    // fonction de déconnexion
    public static function deconnecter(): void
    {
        $connexionModel = new ConnexionModel(new DbConnect());
        // Déconnexion
        $connexionModel->logout();

        //redirection vers la page de connexion
        header("Location: /connexion");
        exit();
    }
}