<?php

namespace blog\models;

class ConnexionModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // fonction de connexion
    public function getID($username): ?int
    {
        //requete pour récupérer l'id de l'utilisateur
        $sql = "SELECT id FROM utilisateurs WHERE username = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':username', $username);

        $stmt->execute();
        $user = $stmt->fetch();
        //retourne l'id de l'utilisateur
        return $user ? (int)$user['id'] : null;
    }

    // fonction pour verifier la connexion
    public function verifConnexion($username, $password): bool
    {
        //requete pour vérifier la connexion
        $sql = "SELECT * FROM utilisateurs WHERE username = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':username', $username);

        $stmt->execute();
        $user = $stmt->fetch();

        //vérification du mot de passe
        if ($user && password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            return true;
        }
        return false;
    }

    //fonction de déconnexion
    public function logout(): void
    {
        //destruction de la session
        //destruction des variables de session
        session_start();
        session_unset();
        session_destroy();
    }
}