<?php

namespace blog\tests;
use PHPUnit\Framework\TestCase;
use blog\controllers\ComparaisonController;
use blog\models\GeoJSONModel;
require_once __DIR__ . '/../controllers/ComparaisonController.php';
require_once __DIR__ . '/../models/GeoJSONModel.php';

class ComparaisonControllerTest extends TestCase
{

    public function testRecupereFichierReturnsCorrectData()
    {
        $_SESSION['dataGeoJson'] = [['type' => 'FeatureCollection']];
        $_SESSION['fileNamesGeojson'] = ['test.geojson'];
        $_SESSION['dataTif'] = ['/tmp/test.tif'];
        $_SESSION['fileNamesTif'] = ['test.tif'];

        $comparaison = new ComparaisonController();
        $result = $comparaison->recupereFichier();


        $this->assertEquals([['type' => 'FeatureCollection']], $result['geojson']);
        $this->assertEquals(['test.geojson'], $result['fileNamesGeojson']);
        $this->assertEquals(['/tmp/test.tif'], $result['tif']);
        $this->assertEquals(['test.tif'], $result['fileNamesTif']);
    }

    public function testRecupereFichierHandlesEmptySession()
    {
        $_SESSION = [];

        $comparaison = new ComparaisonController();
        $result = $comparaison->recupereFichier();

        $this->assertEmpty($result['geojson']);
        $this->assertEmpty($result['fileNamesGeojson']);
        $this->assertEmpty($result['tif']);
        $this->assertEmpty($result['fileNamesTif']);
    }

    public function testAjouterFichierAddsGeoJsonFile()
    {
        $_FILES = [
            'filesToAdd' => [
                'name' => ['test.geojson'],
                'tmp_name' => ['/tmp/test.geojson'],
                'error' => [UPLOAD_ERR_OK]
            ]
        ];
        $_SESSION = [];

        $comparaison = new ComparaisonController();
        $comparaison->ajouterFichier();

        $this->assertNotEmpty($_SESSION['dataGeoJson']);
        $this->assertEquals(['test.geojson'], $_SESSION['fileNamesGeojson']);
    }

    public function testAjouterFichierAddsTifFile()
    {
        $_FILES = [
            'filesToAdd' => [
                'name' => ['test.tif'],
                'tmp_name' => ['/tmp/test.tif'],
                'error' => [UPLOAD_ERR_OK]
            ]
        ];
        $_SESSION = [];

        $comparaison = new ComparaisonController();
        $comparaison->ajouterFichier();

        $this->assertNotEmpty($_SESSION['dataTif']);
        $this->assertEquals(['test.tif'], $_SESSION['fileNamesTif']);
    }

    public function testResetSessionClearsSessionData()
    {
        $_SESSION['dataGeoJson'] = [['type' => 'FeatureCollection']];
        $_SESSION['fileNamesGeojson'] = ['test.geojson'];
        $_SESSION['dataTif'] = ['/tmp/test.tif'];
        $_SESSION['fileNamesTif'] = ['test.tif'];

        ComparaisonController::resetSession();

        $this->assertEmpty($_SESSION['dataGeoJson']);
        $this->assertEmpty($_SESSION['fileNamesGeojson']);
        $this->assertEmpty($_SESSION['dataTif']);
        $this->assertEmpty($_SESSION['fileNamesTif']);
    }


}