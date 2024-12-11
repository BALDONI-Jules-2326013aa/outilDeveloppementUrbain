<?php

namespace blog\models;

class ConnexionModel
{
    private $db;

    public function __construct()
    {
        $dbConnect = new DbConnect();
        $this->db = $dbConnect->connect();
    }

    public function verifConnexion($username, $password): bool
    {
        // Requête pour vérifier si un utilisateur existe avec ce nom et ce mot de passe
        $sql = "SELECT * FROM utilisateurs WHERE username = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':username', $username);
        
        // Exécution de la requête
        $stmt->execute();
        $user = $stmt->fetch();

        // Vérification du mot de passe (en supposant qu'il est haché dans la base de données)
        if ($user && password_verify($password, $user['password'])) {
            return true;
        }
        return false;
    }
}
