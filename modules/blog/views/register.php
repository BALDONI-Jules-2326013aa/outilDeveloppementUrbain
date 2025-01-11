<?php

require_once '/../models/DbConnect.php';
require_once '/../models/InscriptionModel.php';

// Vérifie si la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupère et nettoie les données du formulaire
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Vérifie que tous les champs sont remplis
    if (!empty($email) && !empty($username) && !empty($password) && !empty($nom)) {
        // Connexion à la base de données
        $dbConnect = new \blog\models\DbConnect();
        $db = $dbConnect->connect();
        $inscriptionModel = new \blog\models\InscriptionModel($db);

        // Hash le mot de passe pour le rendre sécurisé
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Enregistre l'utilisateur dans la base de données
        $result = $inscriptionModel->registerUser($email, $username, $hashedPassword, $nom);

        // Vérifie si l'inscription a réussi
        if ($result) {
            // Inscription réussie, redirige vers la page d'accueil
            header('Location: /');
            exit();
        } else {
            // Affiche un message d'erreur si l'inscription a échoué
            echo "L'inscription a échoué. Veuillez réessayer.";
        }
    } else {
        // Affiche un message d'erreur si tous les champs ne sont pas remplis
        echo "Veuillez remplir tous les champs.";
    }
}