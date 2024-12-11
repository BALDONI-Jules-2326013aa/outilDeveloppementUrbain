<?php

namespace blog\models;

class InscriptionModel
{
    private $db;

    public function __construct()
    {
        $dbConnect = new DbConnect();
        $this->db = $dbConnect->connect();
    }

    public function verifInscription($username, $password, $email): bool
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO utilisateurs (username, password, email) VALUES (:username, :password, :email )";
        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':email', $email);

        return $stmt->execute();
    }


}