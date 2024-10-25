<?php

namespace blog\views;
use blog\views\HeadView;
abstract class AbstractView
{
    abstract function css(): string;

    abstract function pageTitle(): string;

    private function header():void
    {

        $logged = isset($_SESSION['logged']) && $_SESSION['logged'] === true;
        $headerview = new HeadView($this->pageTitle(), $this->css(),$logged);
        $headerview->afficher();
    }
    private function footer():void
    {

        include __DIR__ . '/Fragments/footer.html';
    }

    abstract protected function body();

    public function afficher(): void
    {
        $this->header();
        $this->body();
        $this->footer();
    }


}