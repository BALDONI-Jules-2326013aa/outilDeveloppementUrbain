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
        return 'style.css';
    }

    function pageTitle(): string
    {
        return 'Accueil';
    }

    #[Override] public function afficher(): void
    {
        parent::afficher();
    }
}