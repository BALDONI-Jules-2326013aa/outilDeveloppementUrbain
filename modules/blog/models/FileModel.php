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
    public function getOrCreateFolder($folderName, $userId, $parentFolderName = null) {
        // If parentFolderName is an empty string, treat it as null
        if (empty($parentFolderName)) {
            $parentFolderName = null;
        }
    
        // Initialize parent folder ID as null
        $parentFolderId = null;
    
        // If a parent folder name is provided, retrieve its ID
        if ($parentFolderName !== null) {
            $stmt = $this->pdo->prepare("
                SELECT id FROM folders WHERE name = :name AND user_id = :user_id
            ");
            $stmt->bindParam(':name', $parentFolderName);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $parentFolder = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if (!$parentFolder) {
                throw new \Exception("Parent folder not found: $parentFolderName");
            }
    
            $parentFolderId = $parentFolder['id'];
        }
    
        // Check if the folder already exists (with the same name and parent)
        $stmt = $this->pdo->prepare("
            SELECT id FROM folders 
            WHERE name = :name AND user_id = :user_id 
            AND parent_folder_id IS NOT DISTINCT FROM :parent_folder_id
        ");
        $stmt->bindParam(':name', $folderName);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':parent_folder_id', $parentFolderId, PDO::PARAM_INT);
        $stmt->execute();
        $folder = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($folder) {
            // Folder exists, return its ID
            return $folder['id'];
        }
    
        // Create the folder if it doesn't exist
        $stmt = $this->pdo->prepare("
            INSERT INTO folders (name, user_id, parent_folder_id)
            VALUES (:name, :user_id, :parent_folder_id)
        ");
        $stmt->bindParam(':name', $folderName);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':parent_folder_id', $parentFolderId, PDO::PARAM_INT);
        $stmt->execute();
    
        // Return the newly created folder's ID
        return $this->pdo->lastInsertId();
    }
    
    
    public function getFilesByFolder($folderId = null) {
        $stmt = $this->pdo->prepare("
            SELECT id, name, utilisateur_id, folder_id 
            FROM geojson_files 
            WHERE folder_id IS NOT DISTINCT FROM :folder_id
        ");
        $stmt->bindParam(':folder_id', $folderId, PDO::PARAM_INT);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getSubfolders($parentFolderId = null) {
        $stmt = $this->pdo->prepare("
            SELECT id, name 
            FROM folders 
            WHERE parent_folder_id IS NOT DISTINCT FROM :parent_folder_id
        ");
        $stmt->bindParam(':parent_folder_id', $parentFolderId, PDO::PARAM_INT);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
        
    public function getFolderName($folderId) {
        if (empty($folderId) || !is_numeric($folderId)) {
            return null; // Gérer les cas invalides ou renvoyer une valeur par défaut
        }
    
        $stmt = $this->pdo->prepare("SELECT name FROM folders WHERE id = :id");
        $stmt->bindParam(':id', $folderId, PDO::PARAM_INT);
        $stmt->execute();
    
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getFolders() {
        $stmt = $this->pdo->query("SELECT id, name, parent_folder_id FROM folders ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function displayFoldersAndFiles($folders, $files, $parentId = null) {
        echo '<ul>';
        foreach ($folders as $folder) {
            // Vérifiez si le dossier est un enfant du dossier parent actuel
            if ($folder['parent_folder_id'] == $parentId) {
                echo '<li>';
                
                // Ajoutez un élément <details> pour créer un menu déroulant
                echo '<details>';
                echo '<summary>' . htmlspecialchars($folder['name']) . '</summary>';
                
                // Appel récursif pour afficher les sous-dossiers de ce dossier
                $this->displayFoldersAndFiles($folders, $files, $folder['id']);
                
                echo '</details>';
                echo '</li>';
            }
        }
    
        // Affichez les fichiers qui appartiennent au dossier parent actuel
        foreach ($files as $file) {
            if ($file['folder_id'] == $parentId) {
                echo '<li>';
                echo htmlspecialchars($file['name']) . ' (User ID: ' . $file['utilisateur_id'] . ')';
                echo '<br>';
                echo '<button>Télécharger</button>';
                echo '<button>Supprimer</button>';
                echo '</li>';
            }
        }
    
        echo '</ul>';
    }

    
    
    public function uploadFile($name, $geojsonContent, $userId, $folder_id) {


        // Ajoutez un message de débogage pour vérifier le contenu du fichier avant l'insertion
        error_log("Insertion du fichier GeoJSON: " . $geojsonContent);

        $stmt = $this->pdo->prepare("
            INSERT INTO geojson_files (name, geojson, utilisateur_id, folder_id)
            VALUES (:name, :geojson_text, :user_id, :folder_id)
        ");
    
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':geojson_text', $geojsonContent); // Pour sauvegarder le texte brut
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':folder_id', $folder_id); 
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
        $stmt = $this->pdo->query("SELECT id, name, utilisateur_id, folder_id FROM geojson_files ORDER BY id DESC");
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