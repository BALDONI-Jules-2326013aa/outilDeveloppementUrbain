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

    // Fonction pour calculer les aires à partir des coordonnées géographiques
    public function calculerAires($shapefile_path) {
        try {
            // Lecture du fichier Shapefile
            $Shapefile = new ShapefileReader($shapefile_path);
            $aires = [];

            // Parcours des enregistrements du Shapefile
            while ($Shapefile->fetchRecord()) {
                if ($Shapefile->isDeleted()) {
                    continue;
                }

                // Récupération des attributs et des coordonnées
                $attributes = $Shapefile->getRecord();
                if (isset($attributes['Area'])) {
                    // Récupérer les coordonnées du polygone
                    $coordinates = $attributes['coordinates']; // Il faut que les coordonnées soient dans ce format : [[lat, lon], [lat, lon], ...]

                    // Calculer l'aire en utilisant les coordonnées GPS
                    $aire = $this->calculerAireGeographique($coordinates);
                    $aires[] = $aire;
                }
            }

            // Calcul de l'aire la plus petite, la plus grande et l'aire moyenne
            if (count($aires) > 0) {
                $aire_min = min($aires);
                $aire_max = max($aires);
                $aire_moyenne = array_sum($aires) / count($aires);

                // Affichage des résultats
                echo "Aire la plus petite : " . $aire_min . " m²<br>";
                echo "Aire la plus grande : " . $aire_max . " m²<br>";
                echo "Aire moyenne : " . $aire_moyenne . " m²<br>";

                // Appel à la fonction pour générer le Spider Diagram
                $this->afficherSpiderDiagram($aire_min, $aire_moyenne, $aire_max);

            } else {
                echo "Aucune aire n'a été trouvée dans la colonne 'Area'.<br>";
            }

        } catch (Exception $e) {
            echo "Erreur lors de la lecture du Shapefile : " . $e->getMessage();
        }
    }

    // Fonction pour calculer l'aire d'un polygone en utilisant les coordonnées géographiques (latitude, longitude)
    private function calculerAireGeographique($coordinates) {
        $aire = 0;
        $n = count($coordinates);

        // Utilisation de la formule de Gauss pour un polygone sur une sphère
        for ($i = 0; $i < $n; $i++) {
            $lat1 = deg2rad($coordinates[$i][0]);
            $lon1 = deg2rad($coordinates[$i][1]);

            $lat2 = deg2rad($coordinates[($i + 1) % $n][0]);
            $lon2 = deg2rad($coordinates[($i + 1) % $n][1]);

            $aire += ($lon2 - $lon1) * (2 + sin($lat1) + sin($lat2));
        }

        $aire = abs($aire * 6378137.0 * 6378137.0 / 2); // Rayon de la Terre en mètres (WGS-84)

        return $aire; // Aire en m²
    }

    // Fonction pour afficher un Spider Diagram (Diagramme en radar)
    public function afficherSpiderDiagram($aire_min, $aire_moyenne, $aire_max) {
        // Convertir les données en JSON pour les utiliser dans le script JavaScript
        $aire_moyenne = json_encode($aire_moyenne);
        $aire_max = json_encode($aire_max);
        $aire_min = json_encode($aire_min);

        // HTML et script pour le diagramme en radar (Spider Chart)
        echo "
        <canvas id='spiderChart' width='400' height='400'></canvas>
        <script src='https://cdn.jsdelivr.net/npm/chart.js'></script>
        <script>
            var ctx = document.getElementById('spiderChart').getContext('2d');
            var chart = new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: ['Aire Min', 'Aire Moyenne', 'Aire Max'],
                    datasets: [{
                        label: 'Aires des bâtiments',
                        data: [$aire_min, $aire_moyenne, $aire_max],
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scale: {
                        ticks: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>
        ";
    }
}
?>
