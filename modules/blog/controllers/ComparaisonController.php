<?php
namespace blog\controllers;

use blog\models\GeoJSONModel;
use blog\models\ShapefileModel;
use blog\views\ComparaisonView;

class ComparaisonController
{
    private static array $arrayDataShape = [];

    public static function recupereFichier(): array
    {
        session_start();
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

        return [
            'geojson' => $dataGeoJson,
            'fileNamesGeojson' => $fileNamesGeojson,
            'tif' => $dataTif,
            'fileNamesTif' => $fileNamesTif
        ];

    }

    public static function ajouterFichier(): void
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

        header("Location: /comparaison");
        exit();
    }

    public static function resetSession(): void
    {
        session_start();
        unset($_SESSION['dataGeoJson']);
        unset($_SESSION['fileNamesGeojson']);
        unset($_SESSION['dataTif']);
        unset($_SESSION['fileNamesTif']);
        self::$arrayDataShape = [];
    }

    public static function afficheFichier(): void
    {
        $data = self::recupereFichier();
        $dataGeoJson = $data['geojson'];
        $fileNamesGeojson = $data['fileNamesGeojson'];
        $dataTif = $data['tif'];
        $fileNamesTif = $data['fileNamesTif'];
        $view = new ComparaisonView();

        if (!empty($dataGeoJson)) {
            $view->afficherAvecFichiers($dataGeoJson, $fileNamesGeojson);
            $view->afficherGraphiqueBatiments($dataGeoJson, $fileNamesGeojson);
            $view->afficherGraphiqueRadarAireMoyenne($dataGeoJson, $fileNamesGeojson);
        }

        if (!empty($dataTif)) {
            $view->afficheImageTifSurCarte($dataTif);
        }

        $view->afficher();
    }



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
}
