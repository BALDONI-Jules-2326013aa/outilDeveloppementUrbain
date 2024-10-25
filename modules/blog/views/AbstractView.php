<?php
namespace blog\views;

abstract class AbstractView
{
    abstract function css(): string;

    abstract function pageTitle(): string;

    private function header(): void
    {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }


        if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
            include __DIR__ . '/Fragments/header-logged.html'; // Inclure le header pour les utilisateurs connectés
        } else {
            include __DIR__ . '/Fragments/header.html'; // Inclure le header par défaut
        }
    }

    private function footer(): void
    {
        include __DIR__ . '/Fragments/footer.html';
    }

    abstract protected function body();

    public function afficher(): void
    {
        $head = new HeadView($this->pageTitle(), $this->css());
        $head->afficher();
        $this->header();
        $this->body();
        $this->footer();
    }
}
