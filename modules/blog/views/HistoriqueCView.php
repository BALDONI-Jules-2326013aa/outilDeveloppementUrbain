<?php
namespace blog\views;

class HistoriqueCView extends AbstractView {

    /**
     * Affiche le corps de la page d'historique de comparaison.
     */
    protected function body(): void
    {
        include __DIR__ . '/Fragments/historiqueC.html';
    }

    /**
     * Retourne le chemin vers le fichier CSS de la page d'historique de comparaison.
     *
     * @return string Le chemin vers le fichier CSS.
     */
    function css(): string
    {
        return 'historique.css';
    }

    /**
     * Retourne le titre de la page d'historique de comparaison.
     *
     * @return string Le titre de la page.
     */
    function pageTitle(): string
    {
        return 'HistoriqueComparaison';
    }

    /**
     * Affiche la vue en appelant la méthode afficher() de la classe parente.
     */
    #[Override] public function afficher(): void
    {
        parent::afficher();
    }
}