<?php

namespace blog\views;

abstract class AbstractView
{
    // Méthode abstraite pour obtenir le CSS spécifique à la vue
    abstract function css(): string;

    // Méthode abstraite pour obtenir le titre de la page
    abstract function pageTitle(): string;

    // Affiche l'en-tête de la page
    private function header(): void
    {
        // Vérifie si l'utilisateur est connecté
        $loggedin = isset($_SESSION['loggedin']) && $_SESSION['loggedin'];
        // Crée une instance de HeaderView avec l'état de connexion
        $headerView = new HeaderView($loggedin);
        // Affiche l'en-tête
        echo $headerView->afficher();
    }

    // Affiche le pied de page de la page
    private function footer(): void
    {
        // Inclut le fichier HTML du pied de page
        include __DIR__ . '/Fragments/footer.html';
    }

    // Méthode abstraite pour afficher le corps de la page
    abstract protected function body();

    // Affiche la page complète
    public function afficher(): void
    {
        // Crée une instance de HeadView avec le titre de la page et le CSS
        $head = new HeadView($this->pageTitle(), $this->css());
        // Affiche l'en-tête HTML
        $head->afficher();
        // Affiche l'en-tête de la page
        $this->header();
        // Affiche le corps de la page
        $this->body();
        // Affiche le pied de page
        $this->footer();
    }
}