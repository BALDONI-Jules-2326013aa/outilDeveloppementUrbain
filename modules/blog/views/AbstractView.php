<?php

namespace blog\views;

abstract class AbstractView
{
    abstract function css(): string;

    abstract function pageTitle(): string;

    private function header(): void
    {
        $loggedin = isset($_SESSION['loggedin']) && $_SESSION['loggedin'];
        $headerView = new HeaderView($loggedin);
        echo $headerView->afficher();
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