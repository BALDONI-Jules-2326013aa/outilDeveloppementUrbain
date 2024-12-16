<?php
namespace blog\controllers;

use blog\models\FileModel;
use blog\views\FileView;

class FileController {
    private $model;

    public function __construct(FileModel $model) {
        $this->model = $model;
    }

    public static function affichePage(): void {
        session_start();

        $pdo = new \PDO('pgsql:host=postgresql-siti.alwaysdata.net;dbname=siti_db', 'siti', 'motdepassesitia1');
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $model = new FileModel($pdo);
        $files = $model->getFiles();

        $view = new FileView();
        $view->setFiles($files);
        $view->afficher();
    }

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

    private function uploadFile() {
        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['file']['tmp_name'];
            $fileName = $_FILES['file']['name'];
            $fileSize = $_FILES['file']['size'];
            $fileType = $_FILES['file']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            $allowedfileExtensions = array('geojson');
            if (in_array($fileExtension, $allowedfileExtensions)) {
                $fileContent = file_get_contents($fileTmpPath);

                // Ajoutez un message de débogage pour vérifier le contenu du fichier
                error_log("Contenu du fichier GeoJSON: " . $fileContent);

                try {
                    $this->model->uploadFile($fileName, $fileContent, 19); // Remplacez 1 par l'ID utilisateur réel
                    echo "Fichier téléchargé avec succès.";
                    header("Location: /fichier");
                    exit();
                } catch (\Exception $e) {
                    echo "Erreur lors de l'insertion du fichier GeoJSON: " . $e->getMessage();
                }
            } else {
                echo "Type de fichier non autorisé.";
            }
        } else {
            echo "Erreur lors du téléchargement du fichier.";
        }
    }

    private function deleteFile() {
        $fileId = $_POST['file_id'];
        $this->model->deleteFile($fileId);
        header("Location: /fichier");
        exit();
    }

    private function downloadFile() {
        $fileId = $_POST['file_id'];
        $file = $this->model->getFileById($fileId);

        if ($file) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file['name']) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . strlen($file['geojson']));
            echo $file['geojson'];
            exit();
        } else {
            echo "Fichier non trouvé.";
        }
    }

    private function showFiles() {
        $files = $this->model->getFiles();
        $view = new FileView();
        $view->setFiles($files);
        $view->afficher();
    }
}