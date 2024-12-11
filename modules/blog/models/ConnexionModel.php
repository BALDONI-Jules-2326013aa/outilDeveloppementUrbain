<?php

namespace blog\models;

class ConnexionModel
{


    public function __construct()
    {
    }

    public function login($courriel, $password): bool
    {
        $stmt = $this->connect->mysqli()->prepare("SELECT Nom, Code_personnel FROM Tenrac WHERE courriel = ?");
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("s", $courriel);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($db_nom, $db_password);
            $stmt->fetch();

            if (password_verify($password, $db_password)) {
                session_start();
                $_SESSION['loggedin'] = true;
                $_SESSION['courriel'] = $courriel;
                $_SESSION['nom'] = $db_nom;

                $stmt->close();
                return true;
            }
        }

        $stmt->close();
        return false;
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
