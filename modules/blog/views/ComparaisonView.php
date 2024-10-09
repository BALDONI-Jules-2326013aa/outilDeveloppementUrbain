<?php

namespace blog\views;

class ComparaisonView extends AbstractView
{
    protected function body(): void
    {
        include __DIR__ . '/Fragments/comparaison.html';
    }

    function css(): string
    {
        return 'comparaison.css';
    }

    function pageTitle(): string
    {
        return 'Comparaison';
    }

    #[Override] public function afficher(): void
    {
        parent::afficher();
    }
}