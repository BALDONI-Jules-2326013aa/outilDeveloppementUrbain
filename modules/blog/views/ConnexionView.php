<?php
namespace blog\views;

class ConnexionView extends AbstractView {
    protected function body(): void
    {
        include __DIR__ . '/Fragments/connexion.html';
    }

    function css(): string
    {
        return 'connexion.css';
    }


    function pageTitle(): string
    {
        return 'Page de connexion';
    }

    #[Override] public function afficher(): void
    {
        parent::afficher();
    }

}