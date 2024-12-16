<?php
namespace blog\models;

use PDO;

class FileModel {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    public function getPDO() {
        return $this->pdo;
    }


    public function uploadFile($name, $geojsonContent, $userId) {
        // Valider le contenu GeoJSON
        if (!$this->isValidGeoJSON($geojsonContent)) {
            throw new \Exception("Invalid GeoJSON content");
        }

        // Ajoutez un message de débogage pour vérifier le contenu du fichier avant l'insertion
        error_log("Insertion du fichier GeoJSON: " . $geojsonContent);

        $stmt = $this->pdo->prepare("
            INSERT INTO geojson_files (name, geojson, utilisateur_id)
            VALUES (:name, :geojson_text, :user_id)
        ");
    
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':geojson_text', $geojsonContent); // Pour sauvegarder le texte brut
        $stmt->bindParam(':user_id', $userId);
    
        $stmt->execute();
    }

    private function isValidGeoJSON($geojsonContent) {
        $geojson = json_decode($geojsonContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }

        // Vérifiez que le GeoJSON contient les clés nécessaires
        if (!isset($geojson['type']) || !isset($geojson['features'])) {
            return false;
        }

        return true;
    }

    public function getFiles() {
        $stmt = $this->pdo->query("SELECT id, name, utilisateur_id FROM geojson_files ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFileById($fileId) {
        $stmt = $this->pdo->prepare("SELECT * FROM geojson_files WHERE id = :id");
        $stmt->bindParam(':id', $fileId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteFile($fileId) {
        $stmt = $this->pdo->prepare("DELETE FROM geojson_files WHERE id = :id");
        $stmt->bindParam(':id', $fileId, PDO::PARAM_INT);
        $stmt->execute();
    }
}