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
            echo "Registration successful!";
        } else {
            echo "Registration failed. Please try again.";
        }
    } else {
        echo "Please fill in all fields.";
    }
}