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
        $dataArray = [];
        $fileNames = [];

        // Fonction pour traiter les fichiers uploadés
        $processFiles = function ($files) use (&$dataArray, &$fileNames) {
            foreach ($files['tmp_name'] as $key => $tmpName) {
                if (is_uploaded_file($tmpName)) {
                    $fileName = $files['name'][$key];
                    self::checkExtension($fileName, $files, $tmpName);

                    // Lecture des fichiers GeoJSON directement
                    if (pathinfo($fileName, PATHINFO_EXTENSION) === 'geojson') {
                        $data = GeoJSONModel::litGeoJSON($tmpName);
                        if (!empty($data)) {
                            $dataArray[] = $data;
                            $fileNames[] = $fileName;
                        }
                    }
                }
            }
        };

        // Traitement des fichiers uploadés
        if (isset($_FILES['files'])) {
            $processFiles($_FILES['files']);
        }
        if (isset($_FILES['newFiles'])) {
            $processFiles($_FILES['newFiles']);
        }

        // Si des fichiers Shapefile ont été détectés
        if (!empty(self::$arrayDataShape)) {
            foreach (self::$arrayDataShape as $shapeFiles) {
                $geojsonData = ShapefileModel::convertToGeoJSON($shapeFiles);
                if (!empty($geojsonData)) {
                    $dataArray[] = $geojsonData;
                    $fileNames[] = basename($shapeFiles['shp']);
                }
            }
        }

        return [$dataArray, $fileNames];
    }

    public static function afficheFichier(): void
    {
        session_start();
        $data = self::recupereFichier();
        $dataArray = $data[0];
        $fileNames = $data[1];
        $view = new ComparaisonView();
        $view->afficherAvecFichiers($dataArray, $fileNames);
        $view->afficherGraphiqueBatiments($dataArray, $fileNames);
        $view->afficherGraphiqueRadarAireMoyenne($dataArray, $fileNames);
        $view->afficher();
    }

    public static function affichePage(): void
    {
        session_start();
        $view = new ComparaisonView();
        $view->afficher();
    }

    private static function checkExtension(string $fileName, $fichier, string $tmpName): void
    {
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        if (in_array($extension, ['shp', 'shx', 'dbf', 'prj', 'cpg', 'qpj', 'sbn'])) {
            $baseName = pathinfo($fileName, PATHINFO_FILENAME);
            self::$arrayDataShape[$baseName][$extension] = $tmpName;
        } else if ($extension !== 'geojson') {
            echo 'Unsupported file type';
            exit();
        }
    }
}
