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
        // Récupère les fichiers déjà stockés dans la session ou initialise des tableaux vides
        $dataArray = $_SESSION['dataArray'] ?? [];
        $fileNames = $_SESSION['fileNames'] ?? [];

        $processFiles = function ($files) use (&$dataArray, &$fileNames) {
            foreach ($files['tmp_name'] as $key => $tmpName) {
                if (is_uploaded_file($tmpName)) {
                    $fileName = $files['name'][$key];
                    self::checkExtension($fileName, $files, $tmpName);

                    // Gestion des fichiers GeoJSON
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
                    $dataArray[] = $geojsonData;
                    $fileNames[] = basename($shapeFiles['shp']);
                }
            }
        }

        // Mise à jour de la session avec les nouveaux fichiers
        $_SESSION['dataArray'] = $dataArray;
        $_SESSION['fileNames'] = $fileNames;

        return [$dataArray, $fileNames];
    }

    public static function ajouterFichier(): void
    {
        session_start();

        // Récupère les fichiers déjà stockés dans la session
        $dataArray = $_SESSION['dataArray'] ?? [];
        $fileNames = $_SESSION['fileNames'] ?? [];

        if (!empty($_FILES)) {
            foreach ($_FILES['filesToAdd']['tmp_name'] as $key => $tmpName) {
                if (is_uploaded_file($tmpName)) {
                    $fileName = $_FILES['filesToAdd']['name'][$key];

                    // Vérifie si le fichier n'a pas déjà été ajouté pour éviter les doublons
                    if (!in_array($fileName, $fileNames)) {
                        self::checkExtension($fileName, $_FILES['filesToAdd'], $tmpName);

                        // Gestion des fichiers GeoJSON
                        if (pathinfo($fileName, PATHINFO_EXTENSION) === 'geojson') {
                            $data = GeoJSONModel::litGeoJSON($tmpName);
                            if (!empty($data)) {
                                // Ajouter à l'existant au lieu d'écraser
                                $dataArray[] = $data;
                                $fileNames[] = $fileName;
                            }
                        }
                    }
                }
            }
        }

        // Met à jour la session avec les nouveaux fichiers ajoutés, sans écraser les anciens
        $_SESSION['dataArray'] = $dataArray;
        $_SESSION['fileNames'] = $fileNames;

        // Redirige vers la page de comparaison après ajout
        header("Location: /comparaison");
        exit();
    }



    public static function resetSession(): void
    {
        session_start();
        unset($_SESSION['dataArray']);
        unset($_SESSION['fileNames']);
        self::$arrayDataShape = [];
    }

    public static function afficheFichier(): void
    {
        $data = self::recupereFichier();
        $dataArray = $data[0];
        $fileNames = $data[1];
        $view = new ComparaisonView();

        // Affichage des fichiers et des graphiques si des données existent
        if (!empty($dataArray)) {
            $view->afficherAvecFichiers($dataArray, $fileNames);
            $view->afficherGraphiqueBatiments($dataArray, $fileNames);
            $view->afficherGraphiqueRadarAireMoyenne($dataArray, $fileNames);
        }
        echo "<script>console.log(" . json_encode($dataArray) . ")</script>";
        echo "<script>console.log(" . json_encode($fileNames) . ")</script>";
        $view->afficher();
    }

    private static function checkExtension(string $fileName, $fichier, string $tmpName): void
    {
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        if (in_array($extension, ['shp', 'shx', 'dbf', 'prj', 'cpg', 'qpj', 'sbn'])) {
            $baseName = pathinfo($fileName, PATHINFO_FILENAME);
            self::$arrayDataShape[$baseName][$extension] = $tmpName;
        } elseif ($extension !== 'geojson') {
            echo 'Unsupported file type';
            exit();
        }
    }
}
