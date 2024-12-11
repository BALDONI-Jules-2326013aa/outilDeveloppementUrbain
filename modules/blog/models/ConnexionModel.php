<?php

namespace blog\models;

class ConnexionModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function verifConnexion($username, $password): bool
    {
        $sql = "SELECT * FROM utilisateurs WHERE username = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':username', $username);

        $stmt->execute();
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['nom'] = $user['nom'];
            return true;
        }
        return false;
    }
}