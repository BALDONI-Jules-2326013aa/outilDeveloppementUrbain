<?php

namespace blog\models;

class InscriptionModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function registerUser($email, $username, $password): bool
    {
        // Check if email already exists
        $sql = "SELECT COUNT(*) FROM utilisateurs WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        if ($stmt->fetchColumn() > 0) {
            return false; // Email already exists
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO utilisateurs (email, username, password) VALUES (:email, :username, :password)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashedPassword);

        return $stmt->execute();
    }
}