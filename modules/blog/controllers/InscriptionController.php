<?php

namespace blog\controllers;

use blog\views\InscriptionView;
use blog\models\InscriptionModel;
use blog\models\DbConnect;

class InscriptionController
{
    public static function affichePage(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $view = new InscriptionView();
        $view->afficher();
    }

    public static function inscrire(array $post): void
    {
        $email = htmlspecialchars($post["email"] ?? '');
        $username = htmlspecialchars($post["username"] ?? '');
        $password = htmlspecialchars($post["password"] ?? '');

        $dbConnect = new DbConnect();
        $db = $dbConnect->connect();

        if ($dbConnect->isConnected()) {
            $inscriptionModel = new InscriptionModel($db);

            if ($inscriptionModel->registerUser($email, $username, $password)) {
                header("Location: /connexion");
                exit();
            } else {
                echo "L'email existe déjà.";
                exit();
            }
        } else {
            echo "Erreur de connexion à la base de données.";
            exit();
        }
    }
}