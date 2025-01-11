<?php

namespace blog\views;

class SimulationView extends AbstractView
{
    private $body;

    /**
     * Constructeur de la classe SimulationView
     */
    public function __construct() {
        $this->body = '';
    }

    /**
     * Retourne le nom du fichier CSS pour la vue de simulation
     * @return string Le nom du fichier CSS
     */
    public function css(): string
    {
        return 'simulation.css';
    }

    /**
     * Retourne le titre de la page pour la vue de simulation
     * @return string Le titre de la page
     */
    public function pageTitle(): string
    {
        return 'SimulationController';
    }

    /**
     * Affiche le corps de la page
     * @return void
     */
    protected function body(): void
    {
        if (is_readable($this->body)) {
            include $this->body; // Inclut le contenu du corps si lisible
        } else {
            include __DIR__ . '/Fragments/simulation.html'; // Inclut le fichier HTML par défaut
            echo $this->body; // Affiche le contenu du corps
        }
    }

    /**
     * Affiche le formulaire pour les années des fichiers
     * @param array $fileYears Les années des fichiers
     * @param array $fileNames Les noms des fichiers
     * @return void
     */
    public function afficherGetYears(array $fileYears, array $fileNames): void
    {
        // Vérifie s'il y a des années de fichiers à afficher
        if (count($fileYears) > 0) {
            $simulation = "<h2>Simulation</h2>";
        } else {
            $simulation = "<h2>Simulation</h2><p>Aucun fichier n'a été chargé</p>";
        }

        // Génère un formulaire pour chaque année de fichier
        foreach ($fileYears as $key => $fileYear) {
            $simulation .= "<form id='yearsForm' method='post' action='/startSimulation'>
        <label for='year'>Année du fichier $fileNames[$key] : </label>
        <input type='number' id='year' name='year' min='0' value='$fileYear' required>";
        }

        // Ajoute un bouton de soumission si des années de fichiers sont présentes
        if (count($fileYears) > 0) {
            $simulation .= "<input type='submit' value='Lancer la simulation'></form>";
        }

        // Définit le corps de la page avec le formulaire généré
        $this->body = $simulation;
    }

    /**
     * Affiche le résultat de la simulation
     * @param array $dataArray Les données GeoJSON
     * @param array $fileNames Les noms des fichiers GeoJSON
     * @return void
     */
    public function resultatSimulation(array $dataArray, array $fileNames): void
    {
        $geojsonDataJsArray = json_encode($dataArray);
        $fileNamesJsArray = json_encode($fileNames);

        $script =  "<link rel='stylesheet' href='https://unpkg.com/leaflet@1.9.4/dist/leaflet.css' />" .
            "<script src='https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'></script>" .
            "<script>
            const geojsonDataArray = $geojsonDataJsArray;
            const fileNamesArray = $fileNamesJsArray;
          </script>" .
            "<script src='_assets/scripts/map.js'></script>";

        $this->body = $script;
    }

    /**
     * Affiche la vue de simulation
     * @return void
     */
    public function afficher(): void
    {
        parent::afficher();
    }
}