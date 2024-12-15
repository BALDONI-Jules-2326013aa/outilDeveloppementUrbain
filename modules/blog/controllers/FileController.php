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
        if (isset($_FILES['file'])) {
            $error = $_FILES['file']['error'];
            
            if ($error !== UPLOAD_ERR_OK) {
                echo "Erreur lors du téléchargement du fichier : " . $this->codeToMessage($error);
                return;
            }
            
            $fileTmpPath = $_FILES['file']['tmp_name'];
            $fileName = basename($_FILES['file']['name']); // Utiliser basename pour éviter les chemins malveillants
            $fileSize = $_FILES['file']['size'];
            $fileType = $_FILES['file']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));
            
            $allowedfileExtensions = array('geojson');
            $maxFileSize = 2 * 1024 * 1024; // 2MB
            
            if (!in_array($fileExtension, $allowedfileExtensions)) {
                echo "Type de fichier non autorisé. Seuls les fichiers GeoJSON sont acceptés.";
                return;
            }
            
            if ($fileSize > $maxFileSize) {
                echo "Le fichier dépasse la taille maximale autorisée de 2MB.";
                return;
            }
            
            $fileContent = file_get_contents($fileTmpPath);
            if ($fileContent === false) {
                echo "Impossible de lire le contenu du fichier.";
                return;
            }
            
            try {
                echo "<pre>";
                print_r([
                    'Nom du fichier' => $fileName,
                    'Taille' => $fileSize,
                    'Type' => $fileType,
                    'Extension' => $fileExtension,
                    'Contenu' => $fileContent
                ]);
                echo "</pre>";
                
                $this->model->uploadFile($fileName, $fileContent, 19); // Remplacez 1 par l'ID utilisateur réel
                header("Location: /fichier");
                exit();
            } catch (\Exception $e) {
                echo "Une erreur est survenue lors de l'enregistrement du fichier : " . $e->getMessage();
            }
        } else {
            echo "Aucun fichier téléchargé.";
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
    
    private function codeToMessage($code) {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                $message = "Le fichier téléchargé dépasse la directive upload_max_filesize dans php.ini.";
                break;
                case UPLOAD_ERR_FORM_SIZE:
                    $message = "Le fichier téléchargé dépasse la directive MAX_FILE_SIZE spécifiée dans le formulaire HTML.";
                    break;
                    case UPLOAD_ERR_PARTIAL:
                        $message = "Le fichier n'a été que partiellement téléchargé.";
                        break;
                        case UPLOAD_ERR_NO_FILE:
                            $message = "Aucun fichier n'a été téléchargé.";
                            break;
                            case UPLOAD_ERR_NO_TMP_DIR:
                                $message = "Il manque un dossier temporaire.";
                                break;
                                case UPLOAD_ERR_CANT_WRITE:
                                    $message = "Échec de l'écriture du fichier sur le disque.";
                                    break;
                                    case UPLOAD_ERR_EXTENSION:
                                        $message = "Une extension PHP a arrêté le téléchargement du fichier.";
                                        break;
                                        default:
                                        $message = "Erreur inconnue lors du téléchargement du fichier.";
                                        break;
                                    }
                                    return $message;
                                }
                            }
                            