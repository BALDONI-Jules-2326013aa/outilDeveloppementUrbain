<?php

use blog\models\FileModel;

// Connexion à la base de données PostgreSQL
$pdo = new PDO('pgsql:host=postgresql-siti.alwaysdata.net;dbname=siti_db', 'siti', 'motdepassesitia1');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Création d'une instance du modèle de fichier
$model = new FileModel($pdo);

// Récupérez les dossiers et fichiers depuis le contrôleur
$folders = $model->getFolders(); // Inclut id, name, parent_folder_id
$files = $model->getFiles(); // Inclut id, name, utilisateur_id, folder_id

// Affichez les dossiers et fichiers
echo '<h1>Fichiers disponibles</h1>';
$model ->displayFoldersAndFiles($folders, $files);
?>
