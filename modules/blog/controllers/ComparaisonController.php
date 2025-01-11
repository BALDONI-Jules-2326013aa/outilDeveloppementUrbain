<?php
namespace blog\controllers;

use blog\models\GeoJSONModel;
use blog\models\ShapefileModel;
use blog\views\ComparaisonView;
use blog\views\tifView;
use JetBrains\PhpStorm\NoReturn;

/**
 * Classe ComparaisonController
 * Contrôleur pour gérer les fichiers GeoJSON et TIF, les stocker dans la session et les afficher.
 */
class ComparaisonController
{
    private static array $arrayDataShape = [];

    /**
     * Récupère les fichiers GeoJSON et TIF de la session ou des fichiers uploadés.
     * @return array Tableau contenant les données GeoJSON et TIF.
     */
    public static function recupereFichier(): array
    {
        if(!isset($_SESSION)){
            session_start();
        }

        $dataGeoJson = $_SESSION['dataGeoJson'] ?? [];
        $fileNamesGeojson = $_SESSION['fileNamesGeojson'] ?? [];
        $dataTif = $_SESSION['dataTif'] ?? [];
        $fileNamesTif = $_SESSION['fileNamesTif'] ?? [];

        $processFiles = function ($files) use (&$dataGeoJson, &$dataTif, &$fileNamesGeojson, &$fileNamesTif) {
            foreach ($files['tmp_name'] as $key => $tmpName) {
                if (is_uploaded_file($tmpName)) {
                    $fileName = $files['name'][$key];
                    $ext = self::checkExtension($fileName, $tmpName);

                    if ($ext === 'geojson') {
                        $data = GeoJSONModel::litGeoJSON($tmpName);
                        if (!empty($data)) {
                            $dataGeoJson[] = $data;
                            $fileNamesGeojson[] = $fileName;
                        }
                    } else if ($ext === 'tif') {
                        $dataTif[] = $tmpName;
                        $fileNamesTif[] = $fileName;
                    }
                }
            }
        };

        if (!empty($_FILES)) {
            foreach ($_FILES as $fileGroup) {
                if (isset($fileGroup['tmp_name'])) {
                    $processFiles($fileGroup);
                }
            }
        }

        if (!empty(self::$arrayDataShape)) {
            foreach (self::$arrayDataShape as $shapeFiles) {
                $geojsonData = ShapefileModel::convertToGeoJSON($shapeFiles);
                if (!empty($geojsonData)) {
                    $dataGeoJson[] = $geojsonData;
                    $fileNamesGeojson[] = basename($shapeFiles['shp']);
                }
            }
        }

        $_SESSION['dataGeoJson'] = $dataGeoJson;
        $_SESSION['fileNamesGeojson'] = $fileNamesGeojson;
        $_SESSION['dataTif'] = $dataTif;
        $_SESSION['fileNamesTif'] = $fileNamesTif;

        return [
            'geojson' => $dataGeoJson,
            'fileNamesGeojson' => $fileNamesGeojson,
            'tif' => $dataTif,
            'fileNamesTif' => $fileNamesTif
        ];
    }

    /**
     * Ajoute des fichiers GeoJSON et TIF à la session.
     * @return void
     */
    #[NoReturn] public static function ajouterFichier(): void
    {
        session_start();

        $dataGeoJson = $_SESSION['dataGeoJson'] ?? [];
        $fileNamesGeojson = $_SESSION['fileNamesGeojson'] ?? [];
        $dataTif = $_SESSION['dataTif'] ?? [];
        $fileNamesTif = $_SESSION['fileNamesTif'] ?? [];

        if (!empty($_FILES)) {
            foreach ($_FILES['filesToAdd']['tmp_name'] as $key => $tmpName) {
                if (is_uploaded_file($tmpName)) {
                    $fileName = $_FILES['filesToAdd']['name'][$key];

                    if (!in_array($fileName, $fileNamesGeojson) && !in_array($fileName, $fileNamesTif)) {
                        $ext = self::checkExtension($fileName, $tmpName);

                        if ($ext === 'geojson') {
                            $data = GeoJSONModel::litGeoJSON($tmpName);
                            if (!empty($data)) {
                                $dataGeoJson[] = $data;
                                $fileNamesGeojson[] = $fileName;
                            }
                        } elseif ($ext === 'tif') {
                            $dataTif[] = $tmpName;
                            $fileNamesTif[] = $fileName;
                        }
                    }
                }
            }
        }

        $_SESSION['dataGeoJson'] = $dataGeoJson;
        $_SESSION['fileNamesGeojson'] = $fileNamesGeojson;
        $_SESSION['dataTif'] = $dataTif;
        $_SESSION['fileNamesTif'] = $fileNamesTif;

        header("Location: /comparaison");
        exit();
    }

    /**
     * Réinitialise la session en supprimant les données GeoJSON et TIF.
     * @return void
     */
    public static function resetSession(): void
    {
        session_start();
        unset($_SESSION['dataGeoJson']);
        unset($_SESSION['fileNamesGeojson']);
        unset($_SESSION['dataTif']);
        unset($_SESSION['fileNamesTif']);
        self::$arrayDataShape = [];
    }

    /**
     * Affiche les fichiers GeoJSON et TIF sur la page de comparaison.
     * @return void
     */
    public static function afficheFichier(): void {
        $data = self::recupereFichier();
        $dataGeoJson = $data['geojson'];
        $fileNamesGeojson = $data['fileNamesGeojson'];
        $dataTif = $data['tif'];
        $view = new ComparaisonView();
        $autreAffichage = false;

        if (!empty($dataGeoJson)) {
            $view->afficherAvecFichiers($dataGeoJson, $fileNamesGeojson);
            self::afficheGraphiques($dataGeoJson, $fileNamesGeojson, $view);
            (new ComparaisonController)->lanceCalculeTaux($dataGeoJson, $fileNamesGeojson, $view);
        }

        if (!empty($dataTif)) {
            $viewTif = new tifView();
            $viewTif->afficher();
            $autreAffichage = true;
        }

        if (!$autreAffichage){
            $view->afficher();
        }
    }

    /**
     * Vérifie l'extension du fichier et met à jour les données de shapefile si nécessaire.
     * @param string $fileName Nom du fichier.
     * @param string $tmpName Chemin temporaire du fichier.
     * @return string Extension du fichier.
     */
    private static function checkExtension(string $fileName, string $tmpName): string
    {
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
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

    /**
     * Lance le calcul du taux d'erreur entre deux fichiers GeoJSON.
     * @param array $dataArray Données GeoJSON.
     * @param string $fileName Nom du fichier.
     * @param ComparaisonView $view Vue pour afficher les résultats.
     * @return void
     */
    public function lanceCalculeTaux($dataArray, $fileName, $view): void
    {
        $view->afficheComparaisonTestIa($dataArray, $fileName);
    }

    /**
     * Calcule le taux d'erreur entre deux fichiers GeoJSON sélectionnés.
     * @return float|int Taux d'erreur calculé.
     */
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

    /**
     * Affiche la page de comparaison.
     * @return void
     */
    public static function affichePage(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $view = new ComparaisonView();
        $view->afficher();
    }

    /**
     * Affiche les différents graphiques basés sur les données GeoJSON.
     * @param array $dataGeoJson Données GeoJSON.
     * @param array $fileNamesGeojson Noms des fichiers GeoJSON.
     * @param ComparaisonView $view Vue pour afficher les graphiques.
     * @return void
     */
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

        echo '<script>console.log("aireMin dans le controller : ' . $dataAire['aire_min_par_fichier'] . '")</script>';
        echo '<script>console.log("aireMax dans le controller : ' . $dataAire['aire_max_par_fichier'] . '")</script>';

        $view->afficherGraphiqueRecap($dataAire['aire_min_par_fichier'], $dataAire['aire_max_par_fichier'], $fileNamesGeojson);
    }
}