<?php
namespace blog\controllers;

use blog\models\GeoJSONModel;
use blog\models\ShapefileModel;
use blog\models\TifModel;
use blog\views\ComparaisonView;

class ComparaisonController
{
    private static array $arrayDataShape = [];

    public static function recupereFichier(): array
    {
        $dataGeojson = [];
        $dataTif = [];
        $fileNamesGeojson = [];
        $fileNamesTif = [];
        $processFiles = function ($files) use (&$dataGeojson, &$dataTif, &$fileNamesGeojson, &$fileNamesTif) {
            foreach ($files['tmp_name'] as $key => $tmpName) {
                if (is_uploaded_file($tmpName)) {
                    $fileName = $files['name'][$key];
                    $ext = self::checkExtension($fileName, $files, $tmpName);
                    if ($ext === 'geojson') {
                        $data = GeoJSONModel::litGeoJSON($tmpName);
                        if (!empty($data)) {
                            $dataGeojson[] = $data;
                            $fileNamesGeojson[] = $fileName;
                        }
                    }
                    else if ($ext == 'tif') {
                        $dataTif[] = $tmpName;
                        $fileNamesTif[] = $fileName;
                    }
                }
            }
        };

        if (isset($_FILES['files'])) {
            $processFiles($_FILES['files']);
        }
        if (isset($_FILES['newFiles'])) {
            $processFiles($_FILES['newFiles']);
        }

        $dataShape = [];
        $fileNamesShape = [];
        if (!empty(self::$arrayDataShape)) {
            foreach (self::$arrayDataShape as $shapeFiles) {
                $geojsonData = ShapefileModel::convertToGeoJSON($shapeFiles);
                if (!empty($geojsonData)) {
                    $dataShape[] = $geojsonData;
                    $fileNamesShape[] = basename($shapeFiles['shp']);
                }
            }
        }

        return [
            'geojson' => ['data' => $dataGeojson, 'names' => $fileNamesGeojson],
            'tif' => ['data' => $dataTif, 'names' => $fileNamesTif],
            'shape' => ['data' => $dataShape, 'names' => $fileNamesShape]
        ];
    }


    public static function afficheFichier(): void
    {
        session_start();
        $data = self::recupereFichier();
        $dataArrayGeoJson = $data['geojson'];
        $fileNamesGeoJson = $dataArrayGeoJson['names'];
        $dataArrayTif = $data['tif'];
        $fileNamesTif = $dataArrayTif['names'];
        $view = new ComparaisonView();
        if (!empty($dataArrayGeoJson['data'])) {
            $view->afficherAvecFichiers($dataArrayGeoJson['data'], $fileNamesGeoJson);
            $view->afficherGraphiqueBatiments($dataArrayGeoJson['data'], $fileNamesGeoJson);
            $view->afficherGraphiqueRadarAireMoyenne($dataArrayGeoJson['data'], $fileNamesGeoJson);
        }
        else if (!empty($dataArrayTif['data'])) {
            $view->afficheImageTifSurCarte($dataArrayTif['data'], $fileNamesTif);
        }
        $view->afficher();
    }

    public static function affichePage(): void
    {
        session_start();
        $view = new ComparaisonView();
        $view->afficher();
    }

    private static function checkExtension(string $fileName, $fichier, string $tmpName): string
    {
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (in_array($extension, ['shp', 'shx', 'dbf', 'prj', 'cpg', 'qpj', 'sbn'])) {
            $baseName = pathinfo($fileName, PATHINFO_FILENAME);
            self::$arrayDataShape[$baseName][$extension] = $tmpName;
            return "shapefiles";
        }

        else if ($extension === 'geojson'){
            return 'geojson';
        }

        else if ($extension == "tif" || $extension == "tiff"){
            return "tif";
        }

        else{
            return "Pas de fichier reconnnu";
        }
    }
}
