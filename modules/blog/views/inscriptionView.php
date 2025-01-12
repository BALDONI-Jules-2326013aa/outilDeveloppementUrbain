<?php
namespace blog\views;

class inscriptionView extends AbstractView {

    /**
     * Displays the body of the registration page.
     */
    protected function body(): void
    {
        // direction vers la page d'inscription
        include __DIR__ . '/Fragments/inscription.html';
    }

    /**
     * Returns the path to the CSS file for the registration page.
     *
     * @return string The path to the CSS file.
     */
    function css(): string
    {
        // direction vers le fichier css de la page d'inscription
        return 'connexion.css';
    }

    /**
     * Returns the title of the registration page.
     *
     * @return string The title of the page.
     */
    function pageTitle(): string
    {
        // titre de la page d'inscription
        return 'Page d inscription';
    }

    /**
     * Displays the registration page by calling the parent class's afficher() method.
     */
    #[Override] public function afficher(): void
    {
        // affichage de la page d'inscription
        parent::afficher();
    }

}