<?php
namespace blog\controllers;

use blog\views\inscriptionView;
use blog\models\GestionTenracModel;
use blog\models\DbConnect;

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

            if (empty($_POST['email']) || empty($_POST['username']) || empty($_POST['password'])) {
                echo "Tous les champs sont requis.";
                return;
            }

            $newuser = [
                'email' => $_POST['email'],
                'username' => $_POST['username'],
                'password' => $_POST['password'],
            ];

            $dbConnect = new DbConnect();
            $tenracModel = new GestionTenracModel($dbConnect);


            try {
                if ($tenracModel->Inscription(
                    $newuser['email'],
                    $newuser['username'],
                    $newuser['password']
                )) {

                    if (mail($newuser['email'], 'Bienvenue', 'Votre inscription a bien été prise en compte')) {
                        header('Location: /');
                        exit();
                    } else {
                        echo "L'e-mail de bienvenue n'a pas pu être envoyé.";
                    }
                } else {
                    echo "Erreur lors de l'inscription. Veuillez réessayer.";
                }
            } catch (\Exception $e) {
                echo "Une erreur est survenue : " . $e->getMessage();
            }
        }
    }
}
