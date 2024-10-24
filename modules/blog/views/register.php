<?php

require_once 'modules/blog/models/DbConnect.php';
require_once 'modules/blog/models/InscriptionModel.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        $inscriptionModel = new \blog\models\InscriptionModel();
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $result = $inscriptionModel->registerUser($username, $hashedPassword);

        if ($result) {
            // Inscription réussie, redirige vers la page d'accueil
            header('Location: /');
            exit();
        } else {
            echo "L'inscription a échoué. Veuillez réessayer.";
        }
    } else {
        echo "Veuillez remplir tous les champs.";
    }
}