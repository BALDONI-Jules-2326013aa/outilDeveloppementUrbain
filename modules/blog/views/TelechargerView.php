<?php
namespace blog\views;

class TelechargerView extends AbstractView {
    protected function body(): void
    {
        include __DIR__ . '/Fragments/telecharger.html';
    }

    function css(): string
    {
        return 'telecharger.css';
    }

    function pageTitle(): string
    {
        return 'Télécharger des fichiers';
    }

    #[Override] public function afficher(): void
    {
        parent::afficher();
    }
}