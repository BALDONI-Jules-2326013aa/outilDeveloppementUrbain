<?php

class AnalyseController {

    public function uploadGeojson() {
        if (isset($_FILES['geojsonFile'])) {
            $file = $_FILES['geojsonFile'];
            $uploadDir = 'data/';
            $uploadFile = $uploadDir . basename($file['name']);
            
            // Vérification que le fichier est bien un fichier GeoJSON
            $fileType = pathinfo($uploadFile, PATHINFO_EXTENSION);
            if ($fileType != 'geojson') {
                echo json_encode(["error" => "Le fichier doit être au format GeoJSON"]);
                return;
            }

            // Déplacer le fichier vers le répertoire cible
            if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
                echo json_encode(["success" => "Fichier GeoJSON téléchargé avec succès", "filename" => $uploadFile]);
            } else {
                echo json_encode(["error" => "Échec du téléchargement du fichier"]);
            }
        } else {
            echo json_encode(["error" => "Aucun fichier reçu"]);
        }
    }

    // Fonction pour charger le GeoJSON sur la carte
    public function getGeojson() {
        if (isset($_GET['file'])) {
            $file = $_GET['file']; // Chemin spécifique fourni
        } else {
            $file = 'data/carte.geojson';  // Fichier GeoJSON par défaut
        }

        if (file_exists($file)) {
            $geojson = file_get_contents($file);
            header('Content-Type: application/json');
            echo $geojson;
        } else {
            // Aucun fichier n'a été trouvé
            echo json_encode(["error" => "Aucun fichier GeoJSON disponible."]);
        }
    }
}

$controller = new AnalyseController();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->uploadGeojson();
} else {
    $controller->getGeojson();
}
