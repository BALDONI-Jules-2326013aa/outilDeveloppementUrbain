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

    public function connect()
    {
        $this->conn = null;

        try {
            // Changer "mysql" en "pgsql" pour PostgreSQL
            $this->conn = new PDO("pgsql:host=" . $this->host . ";dbname=" . $this->dbName, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "Connexion réussie !";
        } catch (PDOException $e) {
            echo "Erreur de connexion : " . $e->getMessage();
        }

        return $this->conn;
    }
}
