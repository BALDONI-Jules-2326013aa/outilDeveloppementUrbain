<?php
use PHPUnit\Framework\TestCase;
use blog\models\GeoJSONModel;
require_once __DIR__ . '/../models/GeoJSONModel.php';

class GeoJSONModelTest extends TestCase
{

    public function testGeoJSONgetCrs()
    {
        // Contenu mocké du fichier GeoJSON sous forme de tableau associatif
        $mockData = [
            'crs' => [
                'type' => 'name',
                'properties' => [
                    'name' => 'urn:ogc:def:crs:EPSG::4326'
                ]
            ]
        ];

        // Crée une instance de votre modèle ou utilisez la méthode statique
        $geoJSONModel = new GeoJSONModel();
        $result = $geoJSONModel::getGeoJSONCRS($mockData);

        // Vérifiez que le résultat est correct
        $this->assertEquals('EPSG:4326', $result);
    }


    public function testGetGeoJSONYearReturnsYear()
    {
        $mockFile = __DIR__ . '/mock.json';
        file_put_contents($mockFile, json_encode([
            'features' => [
                [
                    'properties' => [
                        'Year' => '2023'
                    ]
                ]
            ]
        ]));

        $geoJSONModel = new GeoJSONModel();
        $result = $geoJSONModel::getGeoJSONYear($mockFile);
        $this->assertEquals('2023', $result);

        unlink($mockFile);
    }

    public function testGetGeoJSONYearReturnsEmptyStringIfYearMissing()
    {
        $mockFile = __DIR__ . '/mock.json';
        file_put_contents($mockFile, json_encode([
            'features' => [
                [
                    'properties' => []
                ]
            ]
        ]));

        $geoJSONModel = new GeoJSONModel();
        $result = $geoJSONModel::getGeoJSONYear($mockFile);
        $this->assertEquals('', $result);

        unlink($mockFile);
    }

    public function testFormuleHaversineCalculatesDistance()
    {
        $lat1 = 48.8566;
        $lon1 = 2.3522;
        $lat2 = 51.5074;
        $lon2 = -0.1278;

        $geoJSONModel = new GeoJSONModel();
        $result = $geoJSONModel::formuleHaversine($lat1, $lon1, $lat2, $lon2);
        $expectedDistance = 343556; // Distance entre Paris et Londres en mètres approximativement

        $this->assertEqualsWithDelta($expectedDistance, $result, 1000); // Tolérance de 1km
    }

    public function testCalculPointCentralCalculatesCorrectCenter()
    {
        $coordinates = [
            [2.0, 48.0],
            [3.0, 49.0],
            [4.0, 50.0]
        ];

        $geoJSONModel = new GeoJSONModel();
        $result = $geoJSONModel::calculPointCentral($coordinates);
        $expected = [
            'lon' => 3.0,
            'lat' => 49.0
        ];

        $this->assertEquals($expected, $result);
    }

    public function testRecupereNombreBatimentWithValidData()
    {
        $fileArray = [
            [
                'features' => [
                    [
                        'geometry' => ['type' => 'Polygon']
                    ],
                    [
                        'geometry' => ['type' => 'MultiPolygon']
                    ],
                    [
                        'geometry' => ['type' => 'Point']
                    ]
                ]
            ],
            [
                'features' => [
                    [
                        'geometry' => ['type' => 'Polygon']
                    ]
                ]
            ]
        ];

        $result = GeoJSONModel::recupereNombreBatiment($fileArray);
        $expected = [2, 1];

        $this->assertEquals($expected, $result);
    }

    public function testRecupereNombreBatimentWithEmptyFeatures()
    {
        $fileArray = [
            ['features' => []],
            []
        ];

        $result = GeoJSONModel::recupereNombreBatiment($fileArray);
        $expected = [0, 0];

        $this->assertEquals($expected, $result);
    }

    public function testRecupereNombreBatimentWithNoPolygonData()
    {
        $fileArray = [
            [
                'features' => [
                    [
                        'geometry' => ['type' => 'Point']
                    ],
                    [
                        'geometry' => ['type' => 'LineString']
                    ]
                ]
            ],
            [
                'features' => [
                    [
                        'geometry' => ['type' => 'Point']
                    ]
                ]
            ]
        ];

        $result = GeoJSONModel::recupereNombreBatiment($fileArray);
        $expected = [0, 0];

        $this->assertEquals($expected, $result);
    }

}
