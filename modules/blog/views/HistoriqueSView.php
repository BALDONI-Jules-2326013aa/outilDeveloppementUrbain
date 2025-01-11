<?php
namespace blog\views;

class HistoriqueSView extends AbstractView {

    // direction vers la page d'historique
    protected function body(): void
    {
        include __DIR__ . '/Fragments/historiqueS.html';
    }

    // direction vers le fichier css de la page d'historique
    function css(): string
    {
        return 'historique.css';
    }

    // titre de la page d'historique
    function pageTitle(): string
    {
        return 'HistoriqueSimulation';
    }

    // affichage de la page d'historique
    #[Override] public function afficher(): void
    {
        parent::afficher();
    }

}

