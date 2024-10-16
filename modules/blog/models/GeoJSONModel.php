<?php

namespace blog\models;

class GeoJSONModel
{
    // Fonction pour lire un fichier GeoJSON et le décoder en tableau
    public static function litGeoJSON($file): array
    {
        $jsonData = file_get_contents($file);
        return json_decode($jsonData, true);
    }

    public static function affichegraphe($AreaArray,$filenameArray): string
    {
        $Area = json_encode($AreaArray);
        $filename = json_encode($filenameArray);
        return "
 <div style='display: none;' id='nbBatimentsJson'>$Area</div>
    <div style='display: none;' id='fileNamesJson'>$filename</div>
        <script>
         document.addEventListener('DOMContentLoaded', function() {
            traiterDonneesGeoJSON(geojsonDataArray);
        });
    </script>
    <div id='map' style='height: 400px;'></div>
    <canvas id='spiderDiagram'></canvas>
    <script src='/_assets/scripts/spiderDiagram.js'></script>";
    }
}
?>
