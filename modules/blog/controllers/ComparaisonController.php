<?php
namespace blog\controllers;

use blog\models\GeoJSONModel;
use blog\models\ShapefileModel;
use blog\views\ComparaisonView;
use blog\views\tifView;
use JetBrains\PhpStorm\NoReturn;

class ComparaisonController
{
    private static array $arrayDataShape = [];

    // Va récupérer les fichiers GeoJSON et TIF qui auront été mis sur la page comparaison
    // Va les stocker dans la session
    // Va retourner un tableau contenant les données GeoJSON et TIF
    public static function recupereFichier(): array
    {
        // Démarre la session si elle n'est pas déjà démarrée
        if(!isset($_SESSION)){
            session_start();
        }

        // Récupère les données GeoJSON et TIF de la session
        $dataGeoJson = $_SESSION['dataGeoJson'] ?? [];
        $fileNamesGeojson = $_SESSION['fileNamesGeojson'] ?? [];
        $dataTif = $_SESSION['dataTif'] ?? [];
        $fileNamesTif = $_SESSION['fileNamesTif'] ?? [];

        // Fonction pour traiter les fichiers uploadés
        $processFiles = function ($files) use (&$dataGeoJson, &$dataTif, &$fileNamesGeojson, &$fileNamesTif) {
            foreach ($files['tmp_name'] as $key => $tmpName) {
                if (is_uploaded_file($tmpName)) {
                    $fileName = $files['name'][$key];
                    $ext = self::checkExtension($fileName, $tmpName);

                    // Gestion des fichiers GeoJSON
                    if ($ext === 'geojson') {
                        $data = GeoJSONModel::litGeoJSON($tmpName);
                        if (!empty($data)) {
                            $dataGeoJson[] = $data;
                            $fileNamesGeojson[] = $fileName;
                        }
                    }
                    // Gestion des fichiers TIF
                    else if ($ext === 'tif') {
                        $dataTif[] = $tmpName;
                        $fileNamesTif[] = $fileName;
                    }
                }
            }
        };

        // Traitement des fichiers nouvellement ajoutés
        if (!empty($_FILES)) {
            foreach ($_FILES as $fileGroup) {
                if (isset($fileGroup['tmp_name'])) {
                    $processFiles($fileGroup);
                }
            }
        }

        // Si des fichiers Shapefile ont été détectés, les traiter
        if (!empty(self::$arrayDataShape)) {
            foreach (self::$arrayDataShape as $shapeFiles) {
                $geojsonData = ShapefileModel::convertToGeoJSON($shapeFiles);
                if (!empty($geojsonData)) {
                    $dataGeoJson[] = $geojsonData;
                    $fileNamesGeojson[] = basename($shapeFiles['shp']);
                }
            }
        }

        // Mise à jour de la session avec les nouveaux fichiers
        $_SESSION['dataGeoJson'] = $dataGeoJson;
        $_SESSION['fileNamesGeojson'] = $fileNamesGeojson;
        $_SESSION['dataTif'] = $dataTif;
        $_SESSION['fileNamesTif'] = $fileNamesTif;

        // Retourne les données GeoJSON et TIF
        return [
            'geojson' => $dataGeoJson,
            'fileNamesGeojson' => $fileNamesGeojson,
            'tif' => $dataTif,
            'fileNamesTif' => $fileNamesTif
        ];
    }

    // Ajoute des fichiers GeoJSON et TIF à la session
    #[NoReturn] public static function ajouterFichier(): void
    {
        session_start();

        $dataGeoJson = $_SESSION['dataGeoJson'] ?? [];
        $fileNamesGeojson = $_SESSION['fileNamesGeojson'] ?? [];
        $dataTif = $_SESSION['dataTif'] ?? [];
        $fileNamesTif = $_SESSION['fileNamesTif'] ?? [];

        // Traitement des fichiers nouvellement ajoutés
        if (!empty($_FILES)) {
            foreach ($_FILES['filesToAdd']['tmp_name'] as $key => $tmpName) {
                if (is_uploaded_file($tmpName)) {
                    $fileName = $_FILES['filesToAdd']['name'][$key];

                    // Vérifie si le fichier a déjà été ajouté pour éviter les doublons
                    if (!in_array($fileName, $fileNamesGeojson) && !in_array($fileName, $fileNamesTif)) {
                        $ext = self::checkExtension($fileName, $tmpName);

                        // Gestion des fichiers GeoJSON
                        if ($ext === 'geojson') {
                            $data = GeoJSONModel::litGeoJSON($tmpName);
                            if (!empty($data)) {
                                $dataGeoJson[] = $data;
                                $fileNamesGeojson[] = $fileName;
                            }
                        }
                        // Gestion des fichiers TIF
                        elseif ($ext === 'tif') {
                            $dataTif[] = $tmpName;
                            $fileNamesTif[] = $fileName;
                        }
                    }
                }
            }
        }

        // Mise à jour de la session avec les nouveaux fichiers ajoutés
        $_SESSION['dataGeoJson'] = $dataGeoJson;
        $_SESSION['fileNamesGeojson'] = $fileNamesGeojson;
        $_SESSION['dataTif'] = $dataTif;
        $_SESSION['fileNamesTif'] = $fileNamesTif;

        // Redirige vers la page de comparaison
        header("Location: /comparaison");
        exit();
    }

    // Réinitialise la session
    public static function resetSession(): void
    {
        session_start();
        unset($_SESSION['dataGeoJson']);
        unset($_SESSION['fileNamesGeojson']);
        unset($_SESSION['dataTif']);
        unset($_SESSION['fileNamesTif']);
        self::$arrayDataShape = [];
    }

    // Affiche les fichiers GeoJSON et TIF sur la page de comparaison
    public static function afficheFichier(): void {
        $data = self::recupereFichier(); // Récupère les données des fichiers
        $dataGeoJson = $data['geojson'];
        $fileNamesGeojson = $data['fileNamesGeojson'];
        $dataTif = $data['tif'];
        $view = new ComparaisonView();
        $autreAffichage = false;

        // Affiche les données GeoJSON si elles ne sont pas vides
        if (!empty($dataGeoJson)) {
            $view->afficherAvecFichiers($dataGeoJson, $fileNamesGeojson);
            self::afficheGraphiques($dataGeoJson, $fileNamesGeojson, $view);
            (new ComparaisonController)->lanceCalculeTaux($dataGeoJson, $fileNamesGeojson, $view);
        }

        // Affiche les données TIF si elles ne sont pas vides
        if (!empty($dataTif)) {
            $viewTif = new tifView();
            $viewTif->afficher();
            $autreAffichage = true;

        }

        // Si aucun fichier n'est ajouté, affiche la page de comparaison
        if (!$autreAffichage){
            $view->afficher();
        }
    }

    // Vérifie l'extension du fichier
    private static function checkExtension(string $fileName, string $tmpName): string
    {
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        // Vérifie les différentes extensions de fichier possibles
        if (in_array($extension, ['shp', 'shx', 'dbf', 'prj', 'cpg', 'qpj', 'sbn'])) {
            $baseName = pathinfo($fileName, PATHINFO_FILENAME);
            self::$arrayDataShape[$baseName][$extension] = $tmpName;
            return "shapefiles";
        } elseif ($extension === 'geojson') {
            return 'geojson';
        } elseif ($extension === 'tif' || $extension === 'tiff') {
            return 'tif';
        } else {
            return "Pas de fichier reconnu";
        }
    }

    public function lanceCalculeTaux($dataArray, $fileName, $view): void
    {
        $view->afficheComparaisonTestIa($dataArray, $fileName);
    }

    public static function calculeTaux(): float | int
    {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['files']) && count($_POST['files']) === 2) {
                $selectedData1 = $_POST['files'][0];
                $selectedData2 = $_POST['files'][1];

                if(isset($_SESSION['fileNamesGeojson']) && isset($_SESSION['dataGeoJson']) ){
                    $indexFichier1 = array_search($selectedData1,$_SESSION['fileNamesGeojson']);
                    $indexFichier2 = array_search($selectedData2,$_SESSION['fileNamesGeojson']);
                    $result1 = $_SESSION['dataGeoJson'][$indexFichier1];
                    $result2 = $_SESSION['dataGeoJson'][$indexFichier2];
                    $geoJsonModel = new GeoJSONModel();
                    return $geoJsonModel->calculeTauxErreurDeuxFichiers($result1, $result2);

                }

            }
        }
        return 0;
    }

    // affiche la page de comparaison
    public static function affichePage(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $view = new ComparaisonView();
        $view->afficher();
    }

    // Affiche les différents graphiques
    public static function afficheGraphiques($dataGeoJson, $fileNamesGeojson, $view): void
    {
        $geoJsonModel = new GeoJSONModel();
        $dataG1 = $geoJsonModel->recupereNombreBatiment($dataGeoJson);
        $view->afficherGraphiqueBatiments($dataG1, $fileNamesGeojson);

        $dataAire = $geoJsonModel->calculerAireMoyMinMax($dataGeoJson);
        $dataG2 = $dataAire['aire_moyenne'];
        $view->afficherGraphiqueRadarAireMoyenne($dataG2, $fileNamesGeojson);

        $dataG3 = $geoJsonModel->recupereDistanceMoyenneBatiments($dataGeoJson);
        $view->afficherGraphiqueDistanceMoyenne($dataG3, $fileNamesGeojson);

        echo '<script>console.log("aireMin dans le controller : ' . $dataAire['aire_min'] . '")</script>';
        echo '<script>console.log("aireMax dans le controller : ' . $dataAire['aire_max'] . '")</script>';

        $view->afficherGraphiqueRecap($dataAire['aire_min'], $dataAire['aire_max'], $fileNamesGeojson);
    }
}