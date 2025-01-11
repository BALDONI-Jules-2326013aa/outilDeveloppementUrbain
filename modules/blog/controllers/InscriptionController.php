<?php

namespace blog\controllers;

use blog\views\inscriptionView;

use blog\models\InscriptionModel;
use blog\models\DbConnect;

class InscriptionController
{
    public static function affichePage(): void
    {
        // Démarrage de la session si elle n'est pas déjà démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $view = new inscriptionView();
        $view->afficher();
    }

    // fonction d'inscription
    public static function inscrire(array $post): void
    {

        $email = htmlspecialchars($post["email"] ?? '');
        $username = htmlspecialchars($post["username"] ?? '');
        $password = htmlspecialchars($post["password"] ?? '');

        // Vérification des champs
        $dbConnect = new DbConnect();
        $db = $dbConnect->connect();

        // Si la connexion à la base de données est établie
        if ($dbConnect->isConnected()) {
            $inscriptionModel = new InscriptionModel($db);

            if ($inscriptionModel->registerUser($email, $username, $password)) {
                //redirection vers la page de connexion
                header("Location: /connexion");
                exit();
            } else {
                // Si l'utilisateur n'est pas trouvé dans la base de données
                echo "L'email existe déjà.";
                exit();
            }
        } else {
            // Si la connexion à la base de données n'est pas établie
            echo "Erreur de connexion à la base de données.";
            exit();
        }
    }
}