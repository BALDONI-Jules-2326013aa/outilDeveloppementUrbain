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
            include __DIR__ . '/Fragments/comparaison.html';
            echo $this->body;
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

    public function afficherAvecFichiers(array $dataArray, array $fileNames): void
    {
        // Encode les données GeoJSON et les noms de fichiers pour les passer au script JavaScript
        $geojsonDataJsArray = json_encode($dataArray);
        $fileNamesJsArray = json_encode($fileNames);

        // Charger les scripts et éléments nécessaires pour la carte
        $script =  "<link rel='stylesheet' href='https://unpkg.com/leaflet@1.9.4/dist/leaflet.css' />" .
         "<script src='https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'></script>" .
         "<script>
            const geojsonDataArray = $geojsonDataJsArray;
            const fileNamesArray = $fileNamesJsArray; // Ajout des noms de fichiers
          </script>" .
         "<script src='_assets/scripts/comparaison.js'></script>";

        $this->body = $script;

        // Appeler la méthode d'affichage parent
        parent::afficher();
    }


    public function afficher(): void
    {
        parent::afficher();
    }
}
