<?php

namespace blog\views;

use http\Message\Body;

class ComparaisonView extends AbstractView
{
    private string $body = __DIR__ . '/Fragments/comparaison.html';
    protected function body(): void
    {
        if (is_readable($this->body)) {
            include $this->body;
        } else {
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

    public function afficherAvecFichier($data): void
    {
        $this->body = "<section id='shapefile-data'><h2>Shapefile Data</h2><pre>{$data}</pre></section></body>";
        parent::afficher();
    }

    public function afficher(): void
    {
        parent::afficher();
    }
}