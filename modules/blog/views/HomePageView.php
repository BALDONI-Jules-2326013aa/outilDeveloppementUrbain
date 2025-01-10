<?php
namespace blog\views;

class HomePageView extends AbstractView {

    // direction vers la page d'accueil
    protected function body(): void
    {
        include __DIR__ . '/Fragments/homePage.html';
    }

    // direction vers le fichier css de la page d'accueil
    function css(): string
    {
        return 'homepage.css';
    }


    // titre de la page d'accueil
    function pageTitle(): string
    {
        return 'Accueil';
    }

    // affichage de la page d'accueil
    #[Override] public function afficher(): void
    {
        parent::afficher();
    }

}