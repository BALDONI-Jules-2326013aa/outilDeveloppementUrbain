<?php

namespace blog\models;

class GeoJSONModel
{
    public static function litGeoJSON($file): array
    {
        $jsonData = file_get_contents($file);
        return json_decode($jsonData, true);
    }

    public static function getGeoJSONYear($file): string
    {
        $content = file_get_contents($file);

        $data = json_decode($content, true);

        if (isset($data['features'][0]['properties']['Year'])) {
            // Retourner l'année de la première feature
            return (string) $data['features'][0]['properties']['Year'];
        }

        return '';
    }


    public static function recupereNombreBaptiment($fileArray): array
    {
        $listNbBatiments = [];
        foreach ($fileArray as $file) {
            $buildingCount = 0;
            if (isset($file['features'])) {
                foreach ($file['features'] as $feature) {
                    if (isset($feature['geometry']['type']) && in_array($feature['geometry']['type'], ['Polygon', 'MultiPolygon'])) {
                        $buildingCount++;
                    }
                }
            }
            $listNbBatiments[] = $buildingCount;
        }

        return $listNbBatiments;
    }


    public static function dessineGraphique($nbBatimentsArray, $fileNameArray): string
    {
        $nbBatimentsJson = json_encode($nbBatimentsArray);
        $fileNamesJson = json_encode($fileNameArray);
        return "    

        <canvas id='barBatiments'></canvas>
        <script src='https://cdn.jsdelivr.net/npm/chart.js'></script>
        <script> 
        let ctx = document.getElementById('barBatiments').getContext('2d');
        let data = {
            labels: $fileNamesJson,
            datasets: [{
                label: 'Nombre de bâtiments',
                data: $nbBatimentsJson,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        }
        let barBatiments = new Chart(ctx,{
            type: 'bar',
            data: data,
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        })
        
        </script>
        ";
    }
}

?>
