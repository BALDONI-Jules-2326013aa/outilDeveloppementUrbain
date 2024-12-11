<?php
namespace blog\models;

use PDO;

class FileModel {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function uploadFile($name, $content, $utilisateur_id) {
        $stmt = $this->pdo->prepare("INSERT INTO public.geojson_files (name, geojson, utilisateur_id) VALUES (:name, :geojson, :utilisateur_id)");
        $stmt->execute([
            ':name' => $name,
            ':geojson' => $content,
            ':utilisateur_id' => $utilisateur_id
        ]);
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