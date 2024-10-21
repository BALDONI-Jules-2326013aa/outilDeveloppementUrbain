<?php

namespace blog\views;
abstract class AbstractView
{
    abstract function css(): string;

    abstract function pageTitle(): string;

    private function header():void
    {

        include __DIR__ . '/Fragments/header.html';
    }
    private function footer():void
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