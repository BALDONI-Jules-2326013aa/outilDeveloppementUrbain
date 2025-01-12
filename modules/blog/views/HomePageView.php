<?php
namespace blog\views;

class HomePageView extends AbstractView {

    /**
     * Affiche le corps de la page d'accueil.
     */
    protected function body(): void
    {
        // direction vers la page d'accueil
        include __DIR__ . '/Fragments/homePage.html';
    }

    /**
     * Retourne le chemin vers le fichier CSS de la page d'accueil.
     *
     * @return string Le chemin vers le fichier CSS.
     */
    function css(): string
    {
        // direction vers le fichier css de la page d'accueil
        return 'homepage.css';
    }

    /**
     * Retourne le titre de la page d'accueil.
     *
     * @return string Le titre de la page.
     */
    function pageTitle(): string
    {
        // titre de la page d'accueil
        return 'Accueil';
    }

    /**
     * Affiche la page d'accueil en appelant la méthode afficher() de la classe parente.
     */
    #[Override] public function afficher(): void
    {
        // affichage de la page d'accueil
        parent::afficher();
    }

}