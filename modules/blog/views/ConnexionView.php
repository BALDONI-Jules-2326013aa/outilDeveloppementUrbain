<?php
namespace blog\views;

class ConnexionView extends AbstractView {

    /**
     * Affiche le corps de la page de connexion.
     */
    protected function body(): void
    {
        // direction vers la page de connexion
        include __DIR__ . '/Fragments/connexion.html';
    }

    /**
     * Retourne le chemin vers le fichier CSS de la page de connexion.
     *
     * @return string Le chemin vers le fichier CSS.
     */
    function css(): string
    {
        // direction vers le fichier css de la page de connexion
        return 'connexion.css';
    }

    /**
     * Retourne le titre de la page de connexion.
     *
     * @return string Le titre de la page de connexion.
     */
    function pageTitle(): string
    {
        // titre de la page de connexion
        return 'Page de connexion';
    }
}