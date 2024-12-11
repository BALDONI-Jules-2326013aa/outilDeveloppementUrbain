<?php
class FileController {
    private $model;

    public function __construct($model) {
        $this->model = $model;
    }

    public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['geojson_file'])) {
            $file = $_FILES['geojson_file'];

            if ($file['error'] === UPLOAD_ERR_OK) {
                $name = $file['name'];
                $content = file_get_contents($file['tmp_name']);
                $utilisateur_id = 1; // ID utilisateur à ajuster selon votre logique

                $this->model->uploadFile($name, $content, $utilisateur_id);
                echo "<p>Fichier \"$name\" ajouté avec succès.</p>";
            } else {
                echo "<p>Erreur lors de l'upload du fichier.</p>";
            }
        }

        $files = $this->model->getFiles();
        include 'views/file_view.php';
    }
}
