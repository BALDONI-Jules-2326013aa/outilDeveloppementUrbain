<?php

namespace blog\views;

class AnalyseView extends AbstractView
{

    public function __construct()
    {
    }

    public function css(): string
    {
        return 'analyse.css';
    }

    public function pageTitle(): string
    {
        return 'Simulation';
    }

    protected function body(): void
    {
        if (is_readable($this->body)) {
            include $this->body;
        } else {
            include __DIR__ . '/Fragments/analyse.html';
            echo $this->body;
        }
    }

    public function afficherSimulation(array $fileYears, array $fileNames): void
    {

        $simulation = '<h1>Récupération des années :</h1></br>';

        foreach ($fileYears as $key => $fileYear) {
            $simulation .= "<h2>Année pour le fichier $fileNames[$key] : $fileYear</h2><br>";
        }

        $this->body = $simulation;

    }



    public function afficher(): void
    {
        parent::afficher();
    }
}
