<?php
namespace blog\views;

class inscriptionView extends AbstractView {
    protected function body(): void
    {
        // direction vers la page d'inscription
        include __DIR__ . '/Fragments/inscription.html';
    }

    function css(): string
    {
        // direction vers le fichier css de la page d'inscription
        return 'connexion.css';
    }


    function pageTitle(): string
    {
        // titre de la page d'inscription
        return 'Page d inscription';
    }

    #[Override] public function afficher(): void
    {
        // affichage de la page d'inscription
        parent::afficher();
    }

}
