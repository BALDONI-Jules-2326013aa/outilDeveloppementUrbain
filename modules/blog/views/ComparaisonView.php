<?php

namespace blog\views;

use http\Message\Body;

class ComparaisonView extends AbstractView
{
    private $body = __DIR__ . '/Fragments/comparaison.html';
    protected function body(): void
    {
        include $this->body;
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
        $this->body = "<section id='shapefile-data'><h2>Shapefile Data</h2><pre>{$data}</pre></section></body>";;
        parent::afficher();
    }

    #[Override] public function afficher(): void
    {
        echo $this->body;
        parent::afficher();
    }
}