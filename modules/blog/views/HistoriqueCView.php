<?php
namespace blog\views;

class HistoriqueCView extends AbstractView {
    protected function body(): void
    {
        include __DIR__ . '/Fragments/historiqueC.html';
    }

    function css(): string
    {
        return 'historique.css';
    }


    function pageTitle(): string
    {
        return 'HistoriqueComparaison';
    }

    #[Override] public function afficher(): void
    {
        parent::afficher();
    }

}
