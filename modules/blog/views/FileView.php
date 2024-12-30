<?php
namespace blog\views;

class FileView extends AbstractView {
    private $files = []; // Initialize with an empty array

    public function setFiles(array $files): void {
        $this->files = $files;
    }

    protected function body(): void {
        include __DIR__ . '/Fragments/File.html';
    }

    function css(): string {
        return 'File.css';
    }

    function pageTitle(): string {
        return 'Page de file';
    }

    public function getFiles(): array {
        return $this->files;
    }
}