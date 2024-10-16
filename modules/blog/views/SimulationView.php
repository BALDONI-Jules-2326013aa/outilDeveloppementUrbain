<?php
namespace blog\views;

class TelechargerView extends AbstractView {
    private array $files;

    public function __construct(array $files)
    {
        $this->files = $files;
    }

    protected function body(): void
    {
        $files = $this->files;
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