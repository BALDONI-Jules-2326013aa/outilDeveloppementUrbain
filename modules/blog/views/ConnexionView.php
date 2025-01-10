<?php
namespace blog\views;

class ConnexionView extends AbstractView {

    protected function body(): void
    {
        // direction vers la page de connexion
        include __DIR__ . '/Fragments/connexion.html';
    }

    function css(): string
    {
        // direction vers le fichier css de la page de connexion
        return 'connexion.css';
    }

    function pageTitle(): string
    {
        // titre de la page de connexion
        return 'Page de connexion';
    }
}
