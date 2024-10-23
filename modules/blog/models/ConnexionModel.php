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

    public static function verifConnexion()
    {
        // Todo avec la base de données
    }
}