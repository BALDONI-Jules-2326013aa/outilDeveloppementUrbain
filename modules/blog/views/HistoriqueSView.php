<?php
namespace blog\views;

class HistoriqueSView extends AbstractView {
    protected function body(): void
    {
        include __DIR__ . '/Fragments/historiqueS.html';
    }

    function css(): string
    {
        return 'historique.css';
    }


    function pageTitle(): string
    {
        return 'HistoriqueSimulation';
    }

    #[Override] public function afficher(): void
    {
        parent::afficher();
    }

}

