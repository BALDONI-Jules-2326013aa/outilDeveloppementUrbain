<?php
namespace blog\models;

use PDO;

class FileModel {
    private $pdo;

    /**
     * Constructeur de FileModel.
     *
     * @param PDO $pdo Instance de la connexion PDO.
     */
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Obtient l'instance PDO.
     *
     * @return PDO L'instance PDO.
     */
    public function getPDO() {
        return $this->pdo;
    }

    /**
     * Obtient ou crée un dossier.
     *
     * @param string $folderName Le nom du dossier.
     * @param int $userId L'ID de l'utilisateur.
     * @param string|null $parentFolderName Le nom du dossier parent (optionnel).
     * @return int L'ID du dossier.
     * @throws \Exception Si le dossier parent n'est pas trouvé.
     */
    public function getOrCreateFolder($folderName, $userId, $parentFolderName = null) {
        if (empty($parentFolderName)) {
            $parentFolderName = null;
        }

        $parentFolderId = null;

        if ($parentFolderName !== null) {
            $stmt = $this->pdo->prepare("
                SELECT id FROM folders WHERE name = :name AND user_id = :user_id
            ");
            $stmt->bindParam(':name', $parentFolderName);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $parentFolder = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$parentFolder) {
                throw new \Exception("Dossier parent non trouvé: $parentFolderName");
            }

            $parentFolderId = $parentFolder['id'];
        }

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
            return $folder['id'];
        }

        $stmt = $this->pdo->prepare("
            INSERT INTO folders (name, user_id, parent_folder_id)
            VALUES (:name, :user_id, :parent_folder_id)
        ");
        $stmt->bindParam(':name', $folderName);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':parent_folder_id', $parentFolderId, PDO::PARAM_INT);
        $stmt->execute();

        return $this->pdo->lastInsertId();
    }

    /**
     * Obtient les fichiers par ID de dossier.
     *
     * @param int|null $folderId L'ID du dossier (optionnel).
     * @return array Les fichiers dans le dossier.
     */
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

    /**
     * Obtient les sous-dossiers par ID de dossier parent.
     *
     * @param int|null $parentFolderId L'ID du dossier parent (optionnel).
     * @return array Les sous-dossiers.
     */
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

    /**
     * Obtient le nom d'un dossier par son ID.
     *
     * @param int $folderId L'ID du dossier.
     * @return array|null Le nom du dossier ou null si non trouvé.
     */
    public function getFolderName($folderId) {
        if (empty($folderId) || !is_numeric($folderId)) {
            return null;
        }

        $stmt = $this->pdo->prepare("SELECT name FROM folders WHERE id = :id");
        $stmt->bindParam(':id', $folderId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtient tous les dossiers.
     *
     * @return array Les dossiers.
     */
    public function getFolders() {
        $stmt = $this->pdo->query("SELECT id, name, parent_folder_id FROM folders ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Affiche les dossiers et fichiers dans une liste imbriquée.
     *
     * @param array $folders Les dossiers.
     * @param array $files Les fichiers.
     * @param int|null $parentId L'ID du dossier parent (optionnel).
     */
    public function displayFoldersAndFiles($folders, $files, $parentId = null) {
        echo '<ul>';
        foreach ($folders as $folder) {
            if ($folder['parent_folder_id'] == $parentId) {
                echo '<li>';
                echo '<details>';
                echo '<summary>' . htmlspecialchars($folder['name']) . '</summary>';
                $this->displayFoldersAndFiles($folders, $files, $folder['id']);
                echo '</details>';
                echo '</li>';
            }
        }

        foreach ($files as $file) {
            if ($file['folder_id'] == $parentId) {
                echo '<li>';
                echo htmlspecialchars($file['name']) . ' (User ID: ' . $file['utilisateur_id'] . ')';
                echo '<br>';
                echo '<form action="/telechargerFichier" method="post" style="display:inline;">';
                echo '<input type="hidden" name="file_id" value="' . htmlspecialchars($file['id']) . '">';
                echo '<button type="submit">Télécharger</button>';
                echo '</form>';
                echo '<form action="/supprimerFichier" method="post" style="display:inline;">';
                echo '<input type="hidden" name="file_id" value="' . htmlspecialchars($file['id']) . '">';
                echo '<input type="hidden" name="action" value="supprimer">';
                echo '<button type="submit">Supprimer</button>';
                echo '</form>';
                echo '</li>';
            }
        }
        echo '</ul>';
    }

    /**
     * Télécharge un fichier GeoJSON.
     *
     * @param string $name Le nom du fichier.
     * @param string $geojsonContent Le contenu du fichier GeoJSON.
     * @param int $userId L'ID de l'utilisateur.
     * @param int $folder_id L'ID du dossier.
     */
    public function uploadFile($name, $geojsonContent, $userId, $folder_id) {
        error_log("Insertion du fichier GeoJSON: " . $geojsonContent);

        $stmt = $this->pdo->prepare("
            INSERT INTO geojson_files (name, geojson, utilisateur_id, folder_id)
            VALUES (:name, :geojson_text, :user_id, :folder_id)
        ");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':geojson_text', $geojsonContent);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':folder_id', $folder_id);
        $stmt->execute();
    }

    /**
     * Valide le contenu GeoJSON.
     *
     * @param string $geojsonContent Le contenu du fichier GeoJSON.
     * @return bool True si valide, sinon false.
     */
    private function isValidGeoJSON($geojsonContent) {
        $geojson = json_decode($geojsonContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }

        if (!isset($geojson['type']) || !isset($geojson['features'])) {
            return false;
        }

        return true;
    }

    /**
     * Obtient tous les fichiers.
     *
     * @return array Les fichiers.
     */
    public function getFiles() {
        $stmt = $this->pdo->query("SELECT id, name, utilisateur_id, folder_id FROM geojson_files ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtient un fichier par son ID.
     *
     * @param int $fileId L'ID du fichier.
     * @return array Les données du fichier.
     */
    public function getFileById($fileId) {
        $stmt = $this->pdo->prepare("SELECT * FROM geojson_files WHERE id = :id");
        $stmt->bindParam(':id', $fileId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Supprime un fichier par son ID.
     *
     * @param int $fileId L'ID du fichier.
     */
    public function deleteFile($fileId) {
        $stmt = $this->pdo->prepare("DELETE FROM geojson_files WHERE id = :id");
        $stmt->bindParam(':id', $fileId, PDO::PARAM_INT);
        $stmt->execute();
    }
}