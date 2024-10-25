<?php

namespace blog\controllers;

use blog\models\ConnexionModel;
use blog\views\ConnexionView;

class ConnexionController
{
    private $model;

    public function __construct()
    {
        $this->model = new ConnexionModel();
    }

    public static function affichePage(): void
    {
        session_start();
        $view = new ConnexionView();
        $view->afficher();
    }
    public function afficherFormulaireConnexion()
    {
        $view = new ConnexionView();
        $view->afficher(); // Affiche uniquement le formulaire
    }

    public function verifierConnexion()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $connexionModel = new ConnexionModel();
            if ($connexionModel->verifConnexion($username, $password)) {
                session_start();
                $_SESSION['username'] = $username;
                $_SESSION['password'] = $password;
                header("Location: /dashboard"); // Redirige après la connexion réussie
                exit;
            } else {
                echo "Nom d'utilisateur ou mot de passe incorrect.";
            }
        } else {
            echo "Données de connexion manquantes.";
        }
    }
    public function connecter(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            if ($this->model->verifConnexion($username, $password)) {
                echo "Connexion réussie";
                // Redirection ou actions après connexion réussie
            } else {
                echo "Nom d'utilisateur ou mot de passe incorrect";
            }
        }
    }
}
