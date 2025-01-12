<?php
namespace blog\views;

class FileView extends AbstractView {
    private $files = [];

    /**
     * Définit les fichiers à afficher.
     *
     * @param array $files Un tableau de fichiers.
     */
    public function setFiles(array $files): void {
        $this->files = $files;
    }

    /**
     * Affiche le corps de la page.
     */
    protected function body(): void {
        include __DIR__ . '/Fragments/File.html';
    }

    /**
     * Retourne le CSS spécifique à la vue.
     *
     * @return string Le chemin vers le fichier CSS.
     */
    function css(): string {
        return 'File.css';
    }

    /**
     * Retourne le titre de la page.
     *
     * @return string Le titre de la page.
     */
    function pageTitle(): string {
        return 'Page de file';
    }

    /**
     * Obtient les fichiers.
     *
     * @return array Un tableau de fichiers.
     */
    public function getFiles(): array {
        return $this->files;
    }
}