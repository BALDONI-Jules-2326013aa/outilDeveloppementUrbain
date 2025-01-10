<?php

namespace blog\models;

class InscriptionModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // fonction d'inscription
    public function registerUser($email, $username, $password): bool
    {
        // Vérification si l'email existe déjà
        $sql = "SELECT COUNT(*) FROM utilisateurs WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        if ($stmt->fetchColumn() > 0) {
            // L'email existe déjà
            return false;
        }
        // Hashage du mot de passe
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        // Requête d'insertion
        $sql = "INSERT INTO utilisateurs (email, username, password) VALUES (:email, :username, :password)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashedPassword);
        return $stmt->execute();
    }
}