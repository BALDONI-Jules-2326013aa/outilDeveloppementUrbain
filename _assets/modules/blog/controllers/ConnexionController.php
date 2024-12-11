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
