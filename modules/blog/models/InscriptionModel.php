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

    public function verifInscription($username, $password,$id): bool
    {
        $sql = "INSERT INTO utilisateurs (username, password,id) VALUES (:username, :password,:id)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}