<?php

require_once '/../models/DbConnect.php';
require_once '/../models/InscriptionModel.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');


    if (!empty($email) && !empty($username) && !empty($password) && !empty($nom)) {
        $dbConnect = new \blog\models\DbConnect();
        $db = $dbConnect->connect();
        $inscriptionModel = new \blog\models\InscriptionModel($db);
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $result = $inscriptionModel->registerUser($email, $username, $hashedPassword, $nom);

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