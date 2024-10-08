<?php
namespace blog\useblog\views;
use blog\views\AbstractView;
use blog\views;

class HomePageView extends AbstractView {
    protected function body(): void
    {
        include __DIR__ . '/Fragments/homePage.html';
    }

    function css(): string
    {
        return 'style.css';
    }

    function pageTitle(): string
    {
        return 'Accueil';
    }

    #[Override] public function afficher(): void
    {
        parent::afficher();
    }

}