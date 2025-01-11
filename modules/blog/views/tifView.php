<?php

namespace blog\views;



class tifView extends AbstractView
{
    // direction vers la page de tif
    protected function body(): void
    {
        include __DIR__ . '/Fragments/formulaireFichier.html';
        include __DIR__ . '/Fragments/tifSurCarte.html';
    }

    // direction vers le fichier css de la page de tif
    function css(): string
    {
        return 'tif.css';
    }

    // titre de la page de tif
    function pageTitle(): string
    {
        return 'Tif image';
    }

    // affichage de la page de tif
    #[Override] public function afficher(): void
    {
        parent::afficher();
    }
}