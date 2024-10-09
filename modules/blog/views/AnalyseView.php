<?php

namespace blog\views;

class AnalyseView extends AbstractView
{
    protected function body(): void
    {
        include __DIR__ . '/Fragments/analyse.html';
    }

    function css(): string
    {
        return 'Analyse.css';
    }

    function pageTitle(): string
    {
        return 'Analyse';
    }

    #[Override] public function afficher(): void
    {
        parent::afficher();
    }
}