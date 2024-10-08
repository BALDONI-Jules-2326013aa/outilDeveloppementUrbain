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

    abstract protected function body();

    public function afficher(): void
    {
        $this->pageTitle();
        $this->css();
        $this->header();
        $this->body();

    }

}