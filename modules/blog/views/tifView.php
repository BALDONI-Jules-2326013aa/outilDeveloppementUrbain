<?php

namespace blog\views;



class tifView extends AbstractView
{
    protected function body(): void
    {
        include __DIR__ . '/Fragments/formulaireFichier.html';
        include __DIR__ . '/Fragments/tifSurCarte.html';
    }

    function css(): string
    {
        return 'tif.css';
    }


    function pageTitle(): string
    {
        return 'Tif image';
    }

    #[Override] public function afficher(): void
    {
        parent::afficher();
    }
}