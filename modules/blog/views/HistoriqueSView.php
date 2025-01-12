<?php
namespace blog\views;

class HistoriqueSView extends AbstractView {

    /**
     * Affiche le corps de la page d'historique.
     */
    protected function body(): void
    {
        include __DIR__ . '/Fragments/historiqueS.html';
    }

    /**
     * Retourne le chemin vers le fichier CSS de la page d'historique.
     *
     * @return string Le chemin vers le fichier CSS.
     */
    function css(): string
    {
        return 'historique.css';
    }

    /**
     * Retourne le titre de la page d'historique.
     *
     * @return string Le titre de la page.
     */
    function pageTitle(): string
    {
        return 'HistoriqueSimulation';
    }

    /**
     * Affiche la page d'historique en appelant la méthode afficher() de la classe parente.
     */
    #[Override] public function afficher(): void
    {
        parent::afficher();
    }

}