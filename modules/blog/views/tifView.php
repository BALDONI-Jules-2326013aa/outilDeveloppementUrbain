<?php

namespace blog\views;

/**
 * Classe tifView
 * Vue pour afficher les fichiers TIF sur une carte.
 */
class tifView extends AbstractView
{
    /**
     * Affiche le corps de la page TIF.
     * @return void
     */
    protected function body(): void
    {
        // Inclut le formulaire de téléchargement de fichiers
        include __DIR__ . '/Fragments/formulaireFichier.html';
        // Inclut la carte pour afficher les fichiers TIF
        include __DIR__ . '/Fragments/tifSurCarte.html';
    }

    /**
     * Retourne le nom du fichier CSS spécifique à cette vue.
     * @return string Le nom du fichier CSS.
     */
    function css(): string
    {
        return 'tif.css';
    }

    /**
     * Retourne le titre de la page TIF.
     * @return string Le titre de la page.
     */
    function pageTitle(): string
    {
        return 'Tif image';
    }

    /**
     * Affiche la page TIF complète.
     * @return void
     */
    #[Override] public function afficher(): void
    {
        parent::afficher();
    }
}