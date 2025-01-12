<?php

namespace blog\models;

use PDO;
use PDOException;

class DbConnect
{
    private $host = 'postgresql-siti.alwaysdata.net';
    private $dbName = 'siti_db';
    private $username = 'siti';
    private $password = 'motdepassesitia1';
    private $conn;

    /**
     * Établit une connexion à la base de données.
     *
     * @return PDO|null L'instance de connexion PDO ou null en cas d'échec.
     */
    public function connect()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO("pgsql:host=" . $this->host . ";dbname=" . $this->dbName, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Erreur de connexion : " . $e->getMessage();
        }

        return $this->conn;
    }

    /**
     * Vérifie si la connexion à la base de données est établie.
     *
     * @return bool True si la connexion est établie, sinon false.
     */
    public function isConnected(): bool
    {
        return $this->conn !== null;
    }
}