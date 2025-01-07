<?php

namespace blog\models;

class ConnexionModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getID($username): ?int
    {
        $sql = "SELECT id FROM utilisateurs WHERE username = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':username', $username);

        $stmt->execute();
        $user = $stmt->fetch();

        return $user ? (int)$user['id'] : null;
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
            return true;
        }
        return false;
    }

    public function logout(): void
    {
        session_start();
        session_unset();
        session_destroy();
    }
}