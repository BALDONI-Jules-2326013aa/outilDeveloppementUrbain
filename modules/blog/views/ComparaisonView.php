<?php

namespace blog\views;

class ComparaisonView extends AbstractView
{
    private string $body = __DIR__ . '/Fragments/comparaison.html';

    protected function body(): void
    {
        if (is_readable($this->body)) {
            include $this->body;  // Inclut le formulaire statique et les éléments de la page
        } else {
            echo "Erreur : le fichier comparaison.html est introuvable.";
        }
    }

    function css(): string
    {
        return 'comparaison.css';
    }

    function pageTitle(): string
    {
        return 'Comparaison';
    }

    public function afficherAvecFichiers(array $dataArray): void
    {
        // Encode les données GeoJSON pour les passer au script JavaScript
        $geojsonDataJsArray = json_encode($dataArray);

        // Charger les scripts et éléments nécessaires pour la carte
        echo "<link rel='stylesheet' href='https://unpkg.com/leaflet@1.9.4/dist/leaflet.css' />";
        echo "<script src='https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'></script>";
        echo "<script>
                const geojsonDataArray = $geojsonDataJsArray;
              </script>";
        echo "<script src='_assets/scripts/comparaison.js'></script>";

        // Appeler la méthode d'affichage parent
        parent::afficher();
    }

    public function afficher(): void
    {
        parent::afficher();
    }
}
