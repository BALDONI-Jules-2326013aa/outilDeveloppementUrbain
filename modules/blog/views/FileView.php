<?php
namespace blog\views;

class FileView extends AbstractView {
    private $files = []; 

    public function setFiles(array $files): void {
        $this->files = $files;
    }

    // Méthode pour afficher le corps de la page
    protected function body(): void {
        include __DIR__ . '/Fragments/File.html';
    }

    // Méthode pour obtenir le CSS spécifique à la vue
    function css(): string {
        return 'File.css';
    }

    // Méthode pour obtenir le titre de la page
    function pageTitle(): string {
        return 'Page de file';
    }

    // Méthode pour obtenir les fichiers
    public function getFiles(): array {
        return $this->files;
    }
}