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
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->dbName, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Erreur de connexion : " . $e->getMessage();
        }

        return $this->conn;
    }
}