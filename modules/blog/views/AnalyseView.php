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

    protected function body()
    {
        include __DIR__ . '/Fragments/analyse.html';
    }

    public function afficherSimulation(array $fileYears, array $fileNames): void
    {
        echo "<h1>Simulation</h1><br>";

        foreach ($fileYears as $key => $fileYear) {
            echo "<h2>Année pour le fichier $fileNames[$key] : $fileYear</h2><br>";
        }

    }
}
