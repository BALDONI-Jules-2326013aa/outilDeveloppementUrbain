<?php
namespace blog\controllers;

use blog\models\FileModel;
use blog\models\ConnexionModel;
use blog\views\FileView;

class FileController {
    private $model;

    /**
     * Constructeur pour initialiser le modèle.
     *
     * @param FileModel $model Le modèle de fichier.
     */
    public function __construct(FileModel $model) {
        $this->model = $model;
    }

    /**
     * Méthode statique pour afficher la page.
     * Démarre une session et affiche les fichiers.
     */
    public static function affichePage(): void {
        session_start();

        // Connexion à la base de données PostgreSQL
        $pdo = new \PDO('pgsql:host=postgresql-siti.alwaysdata.net;dbname=siti_db', 'siti', 'motdepassesitia1');
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        // Récupère les fichiers à partir du modèle
        $model = new FileModel($pdo);
        $files = $model->getFiles();

        // Affiche la vue avec les fichiers
        $view = new FileView();
        $view->setFiles($files);
        $view->afficher();
    }

    /**
     * Méthode pour gérer les requêtes HTTP.
     * Gère les requêtes POST pour téléverser, supprimer ou télécharger des fichiers.
     */
    public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_FILES['file'])) {
                $this->uploadFile();
            } elseif (isset($_POST['file_id']) && isset($_POST['action'])) {
                if ($_POST['action'] === 'supprimer') {
                    $this->deleteFile();
                }
            } elseif (isset($_POST['file_id']) && !isset($_POST['action'])) {
                $this->downloadFile();
            }
        } else {
            $this->showFiles();
        }
    }

    /**
     * Méthode pour téléverser un fichier.
     * Vérifie le type de fichier et l'utilisateur avant de téléverser.
     */
    private function uploadFile() {
        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['file']['tmp_name'];
            $fileName = $_FILES['file']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowedFileExtensions = ['geojson'];

            if (in_array($fileExtension, $allowedFileExtensions)) {
                $fileContent = file_get_contents($fileTmpPath);

                session_start();
                $username = $_COOKIE['courrielSiti'] ?? null;

                if ($username) {
                    $connexionModel = new ConnexionModel($this->model->getPDO());
                    $userId = $connexionModel->getID($username);

                    if ($userId) {
                        // Récupération des noms des dossiers
                        $folderName = $_POST['folder'] ?? null;
                        $parentFolderName = $_POST['parent_folder'] ?? null; // Nom du dossier parent (optionnel)

                        // Création ou récupération du dossier
                        try {
                            $folderId = $this->model->getOrCreateFolder($folderName, $userId, $parentFolderName);

                            // Téléversement du fichier
                            $this->model->uploadFile($fileName, $fileContent, $userId, $folderId);

                            // Redirection en cas de succès
                            header("Location: /fichier");
                            exit();
                        } catch (\Exception $e) {
                            echo "Erreur : " . htmlspecialchars($e->getMessage());
                        }
                    } else {
                        echo "Utilisateur non trouvé.";
                    }
                } else {
                    echo "Utilisateur non connecté.";
                }
            } else {
                echo "Type de fichier non autorisé. Seuls les fichiers .geojson sont acceptés.";
            }
        } else {
            echo "Erreur lors du téléchargement du fichier.";
        }
    }

    /**
     * Méthode pour supprimer un fichier.
     * Supprime le fichier spécifié par l'ID et redirige vers la page des fichiers.
     */
    private function deleteFile() {
        $fileId = $_POST['file_id'];
        $this->model->deleteFile($fileId);
        header("Location: /fichier");
        exit();
    }

    /**
     * Méthode pour télécharger un fichier.
     * Télécharge le fichier spécifié par l'ID.
     */

     private function downloadFile() {
        $fileId = $_POST['file_id'];
        $file = $this->model->getFileById($fileId);
    
        if ($file) {
            $geojson = $file['geojson'];
    
            // Validation JSON
            $decodedGeojson = json_decode($geojson, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                echo "Erreur : le fichier contient un JSON invalide.";
                exit();
            }
    
            // Réencodage propre
            $geojson = json_encode($decodedGeojson);
    
            // Envoi des en-têtes
            header('Content-Description: File Transfer');
            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="' . basename($file['name']) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . strlen($geojson));
    
            // Envoi du fichier
            echo $geojson;
            flush();
            exit();
        } else {
            echo "Fichier non trouvé.";
        }
    }
    
    
    /**
     * Méthode pour afficher les fichiers.
     * Récupère les fichiers à partir du modèle et les affiche.
     */
    private function showFiles() {
        $files = $this->model->getFiles();
        $view = new FileView();
        $view->setFiles($files);
        $view->afficher();
    }
}