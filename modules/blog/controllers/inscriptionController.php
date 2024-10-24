<?php

namespace blog\controllers;

use blog\views\ConnexionView;
use blog\views\HomePageView;
use blog\views\inscriptionView;

class inscriptionController
{
    public static function affichePage(): void
    {
        session_start();
        $view = new inscriptionView();
        $view->afficher();
    }

    public function Inscription(): void
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $newuser = [
                'id' => $_POST['id'],
                'email' => $_POST['email'],
                'username' => $_POST['username'],
                'password' => $_POST['password'],
            ];

            $tenracModel = new GestionTenracModel(new DbConnect());
            $tenracModel->Inscription(
                $newuser['id'],
                $newuser['email'],
                $newuser['username'],
                $newuser['password']
            );
            mail($newuser['email'], 'Bienvenue', 'Votre inscription a bien été prise en compte');
            header('Location: /'); // Redirect to homepage
            exit();
        }
    }
}