<?php
namespace blog\views;

class inscriptionView extends AbstractView {
    protected function body(): void
    {
        include __DIR__ . '/Fragments/inscription.html';
    }

    function css(): string
    {
        return 'connexion.css';
    }


    function pageTitle(): string
    {
        return 'Page d inscription';
    }

    #[Override] public function afficher(): void
    {
        parent::afficher();
    }

}
