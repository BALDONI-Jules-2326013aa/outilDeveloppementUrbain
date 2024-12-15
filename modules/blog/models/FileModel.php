<?php
namespace blog\models;

use PDO;

class FileModel {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function uploadFile($name, $geojsonContent, $userId) {
        $stmt = $this->pdo->prepare("
            INSERT INTO geojson_files (name, geom, geojson, utilisateur_id)
            VALUES (:name, ST_GeomFromGeoJSON(:geojson), :geojson_text, :user_id)
        ");
    
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':geojson', $geojsonContent); // Pour ST_GeomFromGeoJSON()
        $stmt->bindParam(':geojson_text', $geojsonContent); // Pour sauvegarder le texte brut
        $stmt->bindParam(':user_id', $userId);
    
        $stmt->execute();
    }
    
    

    public function getFiles() {
        $stmt = $this->pdo->query("SELECT id, name FROM public.geojson_files ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFileById($fileId) {
        $stmt = $this->pdo->prepare("SELECT * FROM public.geojson_files WHERE id = :id");
        $stmt->bindParam(':id', $fileId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteFile($fileId) {
        $stmt = $this->pdo->prepare("DELETE FROM public.geojson_files WHERE id = :id");
        $stmt->bindParam(':id', $fileId, PDO::PARAM_INT);
        $stmt->execute();
    }
}