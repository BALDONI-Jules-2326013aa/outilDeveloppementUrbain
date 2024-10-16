<?php

namespace blog\models;

class FileModel
{
    public static function getFiles(): array
    {
        // Connexion à la base de données PostgreSQL
        $dsn = 'pgsql:host=localhost;port=8080;dbname=sitidb'; // Changer 'your_database' par le nom de votre DB
        $user = 'postgres'; // Changer 'your_username' par votre nom d'utilisateur PostgreSQL        // Connexion avec PDO
        $pdo = new \PDO($dsn, $user);

        // Requête pour récupérer les fichiers
        $stmt = $pdo->query('SELECT id, filename, filepath FROM files');

        // Retourner les résultats
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
