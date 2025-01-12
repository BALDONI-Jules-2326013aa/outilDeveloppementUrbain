<?php

namespace blog\models;

class ConnexionModel
{
    private $db;

    /**
     * ConnexionModel constructor.
     *
     * @param \PDO $db Instance de la connexion à la base de données.
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Récupère l'ID de l'utilisateur par son nom d'utilisateur.
     *
     * @param string $username Le nom d'utilisateur.
     * @return int|null L'ID de l'utilisateur ou null si non trouvé.
     */
    public function getID($username): ?int
    {
        // Requête pour récupérer l'ID de l'utilisateur
        $sql = "SELECT id FROM utilisateurs WHERE username = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':username', $username);

        $stmt->execute();
        $user = $stmt->fetch();
        // Retourne l'ID de l'utilisateur
        return $user ? (int)$user['id'] : null;
    }

    /**
     * Vérifie les informations de connexion de l'utilisateur.
     *
     * @param string $username Le nom d'utilisateur.
     * @param string $password Le mot de passe.
     * @return bool True si la connexion est réussie, sinon false.
     */
    public function verifConnexion($username, $password): bool
    {
        // Requête pour vérifier la connexion
        $sql = "SELECT * FROM utilisateurs WHERE username = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':username', $username);

        $stmt->execute();
        $user = $stmt->fetch();

        // Vérification du mot de passe
        if ($user && password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            return true;
        }
        return false;
    }

    /**
     * Déconnecte l'utilisateur en détruisant la session.
     */
    public function logout(): void
    {
        // Destruction de la session
        // Destruction des variables de session
        session_start();
        session_unset();
        session_destroy();
    }
}