<?php
namespace blog\controllers;

use blog\models\FileModel;

class FileController {
    private $model;

    public function __construct(FileModel $model) {
        $this->model = $model;
    }

    public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_FILES['file'])) {
                $this->uploadFile();
            } elseif (isset($_POST['file_id']) && isset($_POST['action'])) {
                if ($_POST['action'] === 'modifier') {
                    $this->modifyFile();
                } elseif ($_POST['action'] === 'supprimer') {
                    $this->deleteFile();
                }
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
                $this->model->uploadFile($fileName, $fileContent, 19); // Remplacez 1 par l'ID utilisateur réel
                echo "Fichier téléchargé avec succès.";
                header("Location: /fichier");
                exit();
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

    private function showFiles() {
        $files = $this->model->getFiles();
        include __DIR__ . '/../views/file_view.php';
    }
}